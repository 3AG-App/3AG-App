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
                Section::make('Product Information')
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
                            ->helperText('URL-friendly identifier. Auto-generated from name on creation.'),
                        Select::make('type')
                            ->options(ProductType::class)
                            ->required()
                            ->native(false),
                        Textarea::make('short_description')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Brief description of the product...'),
                        RichEditor::make('long_description')
                            ->columnSpanFull()
                            ->placeholder('Detailed product description...'),
                    ]),
                Section::make('Settings')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->helperText('Inactive products are hidden from the storefront.')
                            ->default(true),
                        TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->helperText('Lower numbers appear first.'),
                    ]),
                Section::make('Banner Image')
                    ->columnSpanFull()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('banner')
                            ->collection('banner')
                            ->image()
                            ->maxSize(5120)
                            ->imageEditor()
                            ->imagePreviewHeight('200')
                            ->downloadable()
                            ->openable()
                            ->helperText('Upload a banner image for the product page hero (max 5MB). Recommended: 1920x1080.'),
                    ]),
                Section::make('Screenshots')
                    ->columnSpanFull()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('screenshots')
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
                            ->helperText('Upload product screenshots (max 5MB each). Drag to reorder.'),
                    ]),
            ]);
    }
}
