<?php

use App\Enums\CsvUploadStatus;
use App\Jobs\UploadNaldaCsvToSftp;
use App\Models\NaldaCsvUpload;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use phpseclib3\Net\SFTP;

it('can be serialized with encrypted password', function () {
    $csvUpload = NaldaCsvUpload::factory()->create();
    $encryptedPassword = Crypt::encryptString('test-password');

    $job = new UploadNaldaCsvToSftp($csvUpload, $encryptedPassword);

    expect($job->csvUpload->id)->toBe($csvUpload->id)
        ->and($job->encryptedPassword)->toBe($encryptedPassword);
});

it('is dispatched to the queue', function () {
    Queue::fake();

    $csvUpload = NaldaCsvUpload::factory()->create();
    $encryptedPassword = Crypt::encryptString('test-password');

    UploadNaldaCsvToSftp::dispatch($csvUpload, $encryptedPassword);

    Queue::assertPushed(UploadNaldaCsvToSftp::class, function ($job) use ($csvUpload) {
        return $job->csvUpload->id === $csvUpload->id;
    });
});

it('marks upload as failed when csv file is not found', function () {
    $csvUpload = NaldaCsvUpload::factory()->create(['status' => CsvUploadStatus::Pending]);
    $encryptedPassword = Crypt::encryptString('test-password');

    $job = new UploadNaldaCsvToSftp($csvUpload, $encryptedPassword);
    $job->handle();

    $csvUpload->refresh();

    expect($csvUpload->status)->toBe(CsvUploadStatus::Failed)
        ->and($csvUpload->error_message)->toBe('CSV file not found.');
});

it('marks upload as failed when the sftp transfer is interrupted', function () {
    Storage::fake('nalda-csv');

    $csvUpload = NaldaCsvUpload::factory()->create([
        'status' => CsvUploadStatus::Pending,
        'sftp_path' => '/uploads',
    ]);

    $disk = Storage::disk('nalda-csv');
    $disk->put('incoming/orders.csv', "sku,qty\nABC,1\n");

    $filePath = $disk->path('incoming/orders.csv');
    $csvUpload->addMedia($filePath)
        ->usingFileName('orders.csv')
        ->toMediaCollection('csv');

    $encryptedPassword = Crypt::encryptString('test-password');

    $sftp = \Mockery::mock(SFTP::class);
    $sftp->shouldReceive('setTimeout')->once()->with(120);
    $sftp->shouldReceive('setKeepAlive')->once()->with(10);
    $sftp->shouldReceive('login')
        ->once()
        ->with($csvUpload->sftp_username, 'test-password')
        ->andReturnTrue();
    $sftp->shouldReceive('mkdir')
        ->once()
        ->with('/uploads', -1, true)
        ->andReturnTrue();
    $sftp->shouldReceive('put')
        ->once()
        ->andThrow(new \UnexpectedValueException('Expected NET_SFTP_STATUS. Got packet type: '));
    $sftp->shouldReceive('disconnect')->once()->andReturnNull();

    $job = new class($csvUpload, $encryptedPassword, $sftp) extends UploadNaldaCsvToSftp
    {
        public function __construct(
            NaldaCsvUpload $csvUpload,
            string $encryptedPassword,
            private SFTP $sftp
        ) {
            parent::__construct($csvUpload, $encryptedPassword);
        }

        protected function createSftp(string $host, int $port, int $timeout): SFTP
        {
            return $this->sftp;
        }
    };

    $exceptionMessage = null;

    try {
        $job->handle();
    } catch (\RuntimeException $exception) {
        $exceptionMessage = $exception->getMessage();
    }

    $csvUpload->refresh();

    expect($exceptionMessage)->toContain('SFTP connection dropped while uploading file to /uploads/orders.csv')
        ->and($csvUpload->status)->toBe(CsvUploadStatus::Failed)
        ->and($csvUpload->error_message)->toBe($exceptionMessage);
});

it('retries when the sftp server responds with status failure', function () {
    Storage::fake('nalda-csv');

    $csvUpload = NaldaCsvUpload::factory()->create([
        'status' => CsvUploadStatus::Pending,
        'sftp_path' => '/uploads',
    ]);

    $disk = Storage::disk('nalda-csv');
    $disk->put('incoming/products.csv', "sku,qty\nABC,1\n");

    $filePath = $disk->path('incoming/products.csv');
    $csvUpload->addMedia($filePath)
        ->usingFileName('products.csv')
        ->toMediaCollection('csv');

    $encryptedPassword = Crypt::encryptString('test-password');

    $sftp = \Mockery::mock(SFTP::class);
    $sftp->shouldReceive('setTimeout')->once()->with(120);
    $sftp->shouldReceive('setKeepAlive')->once()->with(10);
    $sftp->shouldReceive('login')
        ->once()
        ->with($csvUpload->sftp_username, 'test-password')
        ->andReturnTrue();
    $sftp->shouldReceive('mkdir')
        ->once()
        ->with('/uploads', -1, true)
        ->andReturnTrue();
    $sftp->shouldReceive('put')
        ->once()
        ->andReturnFalse();
    $sftp->shouldReceive('getLastSFTPError')
        ->once()
        ->andReturn('NET_SFTP_STATUS_FAILURE: failure');
    $sftp->shouldReceive('put')
        ->once()
        ->andReturnTrue();
    $sftp->shouldReceive('disconnect')->once()->andReturnNull();

    $job = new class($csvUpload, $encryptedPassword, $sftp) extends UploadNaldaCsvToSftp
    {
        public function __construct(
            NaldaCsvUpload $csvUpload,
            string $encryptedPassword,
            private SFTP $sftp
        ) {
            parent::__construct($csvUpload, $encryptedPassword);
        }

        protected function createSftp(string $host, int $port, int $timeout): SFTP
        {
            return $this->sftp;
        }

        protected function sleepBetweenAttempts(int $attempt): void {}
    };

    $job->handle();

    $csvUpload->refresh();

    expect($csvUpload->status)->toBe(CsvUploadStatus::Completed)
        ->and($csvUpload->sftp_path)->toBe('/uploads/products.csv')
        ->and($csvUpload->error_message)->toBeNull();
});
