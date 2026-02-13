<?php

namespace App\Filament\Resources\NaldaCsvUploads\Schemas;

use App\Enums\NaldaCsvType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NaldaCsvUploadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.resources.nalda_csv_uploads.form.sections.upload_details'))
                    ->columnSpanFull()
                    ->schema([
                        Select::make('license_id')
                            ->relationship('license', 'license_key')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('domain')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder(__('admin.resources.nalda_csv_uploads.form.placeholders.domain')),
                                Select::make('csv_type')
                                    ->options(NaldaCsvType::class)
                                    ->required()
                                    ->native(false),
                            ]),
                        SpatieMediaLibraryFileUpload::make('csv')
                            ->collection('csv')
                            ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'])
                            ->maxSize(10240)
                            ->downloadable()
                            ->openable()
                            ->helperText(__('admin.resources.nalda_csv_uploads.form.help.csv_upload')),
                    ]),
                Section::make(__('admin.resources.nalda_csv_uploads.form.sections.sftp_configuration'))
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('sftp_host')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder(__('admin.resources.nalda_csv_uploads.form.placeholders.sftp_host')),
                                TextInput::make('sftp_port')
                                    ->numeric()
                                    ->default(22)
                                    ->minValue(1)
                                    ->maxValue(65535),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('sftp_username')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('sftp_path')
                                    ->maxLength(255)
                                    ->placeholder(__('admin.resources.nalda_csv_uploads.form.placeholders.sftp_path')),
                            ]),
                    ]),
                Section::make(__('admin.common.status'))
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->options([
                                        'pending' => __('admin.resources.nalda_csv_uploads.form.status.pending'),
                                        'processing' => __('admin.resources.nalda_csv_uploads.form.status.processing'),
                                        'completed' => __('admin.resources.nalda_csv_uploads.form.status.completed'),
                                        'failed' => __('admin.resources.nalda_csv_uploads.form.status.failed'),
                                    ])
                                    ->default('pending')
                                    ->native(false),
                                TextInput::make('uploaded_at')
                                    ->disabled()
                                    ->placeholder(__('admin.resources.nalda_csv_uploads.form.placeholders.uploaded_at_auto')),
                            ]),
                        Textarea::make('error_message')
                            ->rows(3)
                            ->placeholder(__('admin.resources.nalda_csv_uploads.form.placeholders.error_message')),
                    ])
                    ->collapsed()
                    ->collapsible(),
            ]);
    }
}
