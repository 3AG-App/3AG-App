<?php

namespace App\Filament\Resources\NaldaCsvUploads\Schemas;

use App\Enums\CsvUploadStatus;
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
                            ->label(__('admin.resources.license_activations.table.license'))
                            ->relationship('license', 'license_key')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('domain')
                                    ->label(__('admin.resources.licenses.relation_activations.columns.domain'))
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder(__('admin.resources.nalda_csv_uploads.form.placeholders.domain')),
                                Select::make('csv_type')
                                    ->label(__('admin.common.type'))
                                    ->options(NaldaCsvType::class)
                                    ->required()
                                    ->native(false),
                            ]),
                        SpatieMediaLibraryFileUpload::make('csv')
                            ->label(__('admin.resources.nalda_csv_uploads.form.fields.csv_file'))
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
                                    ->label(__('admin.resources.nalda_csv_uploads.form.fields.sftp_host'))
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder(__('admin.resources.nalda_csv_uploads.form.placeholders.sftp_host')),
                                TextInput::make('sftp_port')
                                    ->label(__('admin.resources.nalda_csv_uploads.form.fields.sftp_port'))
                                    ->numeric()
                                    ->default(22)
                                    ->minValue(1)
                                    ->maxValue(65535),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('sftp_username')
                                    ->label(__('admin.resources.nalda_csv_uploads.form.fields.sftp_username'))
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('sftp_path')
                                    ->label(__('admin.resources.nalda_csv_uploads.form.fields.sftp_path'))
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
                                    ->label(__('admin.common.status'))
                                    ->options(CsvUploadStatus::class)
                                    ->default(CsvUploadStatus::Pending->value)
                                    ->native(false),
                                TextInput::make('uploaded_at')
                                    ->label(__('admin.resources.nalda_csv_uploads.form.fields.uploaded_at'))
                                    ->disabled()
                                    ->placeholder(__('admin.resources.nalda_csv_uploads.form.placeholders.uploaded_at_auto')),
                            ]),
                        Textarea::make('error_message')
                            ->label(__('admin.resources.nalda_csv_uploads.form.fields.error_message'))
                            ->rows(3)
                            ->placeholder(__('admin.resources.nalda_csv_uploads.form.placeholders.error_message')),
                    ])
                    ->collapsed()
                    ->collapsible(),
            ]);
    }
}
