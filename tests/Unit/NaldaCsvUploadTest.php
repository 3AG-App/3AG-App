<?php

use App\Enums\NaldaCsvType;
use App\Models\NaldaCsvUpload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('falls back to enum sftp folder when sftp_path is empty', function (?string $path) {
    $upload = NaldaCsvUpload::factory()->create([
        'csv_type' => NaldaCsvType::Products,
        'sftp_path' => $path,
    ]);

    expect($upload->getResolvedSftpFolder())->toBe('/');
})->with([
    'null' => [null],
    'empty' => [''],
    'whitespace' => ['   '],
]);

it('normalizes custom sftp path', function (string $path, string $expected) {
    $upload = NaldaCsvUpload::factory()->create([
        'csv_type' => NaldaCsvType::Orders,
        'sftp_path' => $path,
    ]);

    expect($upload->getResolvedSftpFolder())->toBe($expected);
})->with([
    'leading slash' => ['/uploads/csv', '/uploads/csv'],
    'trailing slash' => ['/uploads/csv/', '/uploads/csv'],
    'missing leading slash' => ['uploads/csv', '/uploads/csv'],
    'root' => ['/', '/'],
]);
