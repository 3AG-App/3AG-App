<?php

namespace App\Jobs;

use App\Models\NaldaCsvUpload;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Crypt;
use phpseclib3\Net\SFTP;

class UploadNaldaCsvToSftp implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    private const CONNECT_TIMEOUT_SECONDS = 30;

    private const TRANSFER_TIMEOUT_SECONDS = 120;

    private const KEEPALIVE_SECONDS = 10;

    private const PUT_RETRY_ATTEMPTS = 3;

    private const PUT_RETRY_DELAY_MS = 500;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public NaldaCsvUpload $csvUpload,
        public string $encryptedPassword
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $csvFile = $this->csvUpload->getCsvFile();

        if (! $csvFile) {
            $this->csvUpload->markAsFailed('CSV file not found.');

            return;
        }

        $localFilePath = $csvFile->getPath();
        $originalFilename = $csvFile->file_name;

        try {
            $password = Crypt::decryptString($this->encryptedPassword);

            $sftpPath = $this->uploadToSftp(
                $this->csvUpload->sftp_host,
                $this->csvUpload->sftp_port,
                $this->csvUpload->sftp_username,
                $password,
                $this->csvUpload->getResolvedSftpFolder(),
                $localFilePath,
                $originalFilename
            );

            $this->csvUpload->markAsUploaded($sftpPath);
        } catch (\Exception $e) {
            $this->csvUpload->markAsFailed($e->getMessage());

            throw $e;
        }
    }

    private function uploadToSftp(
        string $host,
        int $port,
        string $username,
        string $password,
        string $remoteFolder,
        string $localFilePath,
        string $originalFilename
    ): string {
        $sftp = $this->makeSftp($host, $port);

        try {
            if (! $sftp->login($username, $password)) {
                throw new \RuntimeException('SFTP authentication failed.');
            }

            $remotePath = rtrim($remoteFolder, '/').'/'.$originalFilename;

            if ($remoteFolder !== '/') {
                $sftp->mkdir($remoteFolder, -1, true);
            }

            if (! file_exists($localFilePath)) {
                throw new \RuntimeException("Local file not found: {$localFilePath}");
            }

            if (! is_readable($localFilePath)) {
                throw new \RuntimeException("Local file not readable: {$localFilePath}");
            }

            try {
                for ($attempt = 1; $attempt <= self::PUT_RETRY_ATTEMPTS; $attempt++) {
                    if ($sftp->put($remotePath, $localFilePath, SFTP::SOURCE_LOCAL_FILE)) {
                        break;
                    }

                    $lastError = $sftp->getLastSFTPError() ?: $sftp->getLastError() ?: 'Unknown SFTP error';

                    if ($attempt < self::PUT_RETRY_ATTEMPTS && $this->isRetryableSftpFailure($lastError)) {
                        $this->sleepBetweenAttempts($attempt);

                        continue;
                    }

                    throw new \RuntimeException("Failed to upload file to {$remotePath}: {$lastError}");
                }
            } catch (\UnexpectedValueException $exception) {
                $message = "SFTP connection dropped while uploading file to {$remotePath}: {$exception->getMessage()}";
                throw new \RuntimeException($message, 0, $exception);
            }

            return $remotePath;
        } finally {
            $sftp->disconnect();
        }
    }

    protected function createSftp(string $host, int $port, int $timeout): SFTP
    {
        return new SFTP($host, $port, $timeout);
    }

    protected function makeSftp(string $host, int $port): SFTP
    {
        $sftp = $this->createSftp($host, $port, self::CONNECT_TIMEOUT_SECONDS);

        $sftp->setTimeout(self::TRANSFER_TIMEOUT_SECONDS);
        $sftp->setKeepAlive(self::KEEPALIVE_SECONDS);

        return $sftp;
    }

    protected function isRetryableSftpFailure(string $error): bool
    {
        return str_contains($error, 'NET_SFTP_STATUS_FAILURE');
    }

    protected function sleepBetweenAttempts(int $attempt): void
    {
        usleep(self::PUT_RETRY_DELAY_MS * 1000 * $attempt);
    }
}
