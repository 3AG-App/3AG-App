<?php

use App\Models\Product;
use App\Models\Release;
use Illuminate\Http\UploadedFile;

it('belongs to a product', function () {
    $product = Product::factory()->create();
    $release = Release::factory()->create(['product_id' => $product->id]);

    expect($release->product->is($product))->toBeTrue();
});

it('stores a zip file in the zip media collection', function () {
    $release = Release::factory()->create();

    $release->addMedia(UploadedFile::fake()->create('release.zip', 128, 'application/zip'))
        ->toMediaCollection('zip');

    expect($release->getZipFile())->not->toBeNull()
        ->and($release->getZipFile()?->collection_name)->toBe('zip')
        ->and($release->getZipFile()?->disk)->toBe('media');
});

it('keeps only one zip file per release', function () {
    $release = Release::factory()->create();

    $release->addMedia(UploadedFile::fake()->create('release-1.zip', 128, 'application/zip'))
        ->toMediaCollection('zip');
    $release->addMedia(UploadedFile::fake()->create('release-2.zip', 128, 'application/zip'))
        ->toMediaCollection('zip');

    expect($release->getMedia('zip'))->toHaveCount(1)
        ->and($release->getZipFile()?->file_name)->toBe('release-2.zip');
});
