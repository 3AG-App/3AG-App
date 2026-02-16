<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\ProductType;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.resources.products.form.sections.product_information'))
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set, $context) => $context === 'create'
                                ? $set('slug', Str::slug($state))
                                : null
                            ),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText(__('admin.resources.products.form.help.slug')),
                        Select::make('type')
                            ->options(ProductType::class)
                            ->required()
                            ->native(false),
                        Textarea::make('short_description')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder(__('admin.resources.products.form.placeholders.short_description')),
                        RichEditor::make('long_description')
                            ->columnSpanFull()
                            ->placeholder(__('admin.resources.products.form.placeholders.long_description')),
                    ]),
                Section::make(__('admin.resources.products.form.sections.settings'))
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->label(__('admin.common.active'))
                            ->helperText(__('admin.resources.products.form.help.inactive_hidden'))
                            ->default(true),
                        TextInput::make('sort_order')
                            ->label(__('admin.resources.products.fields.sort_order'))
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->helperText(__('admin.resources.products.form.help.sort_order')),
                    ]),
                Section::make(__('admin.resources.products.form.sections.banner_image'))
                    ->columnSpanFull()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('banner')
                            ->label(__('admin.resources.products.form.fields.banner_image'))
                            ->collection('banner')
                            ->image()
                            ->maxSize(5120)
                            ->imageEditor()
                            ->imagePreviewHeight('200')
                            ->downloadable()
                            ->openable()
                            ->helperText(__('admin.resources.products.form.help.banner_upload')),
                    ]),
                Section::make(__('admin.resources.products.form.sections.screenshots'))
                    ->columnSpanFull()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('screenshots')
                            ->label(__('admin.resources.products.form.fields.screenshots'))
                            ->collection('screenshots')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->preserveFilenames()
                            ->maxSize(5120)
                            ->imageEditor()
                            ->imagePreviewHeight('200')
                            ->panelLayout('grid')
                            ->downloadable()
                            ->openable()
                            ->helperText(__('admin.resources.products.form.help.screenshots_upload')),
                    ]),
            ]);
    }
}
