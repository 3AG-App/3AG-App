<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ReleasesRelationManager extends RelationManager
{
    protected static string $relationship = 'releases';

    protected static \BackedEnum|string|null $icon = Heroicon::OutlinedArrowDownTray;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.resources.products.relation_releases.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('version')
                    ->label(__('admin.resources.products.relation_releases.fields.version'))
                    ->required()
                    ->maxLength(255),
                Textarea::make('release_notes')
                    ->label(__('admin.resources.products.relation_releases.fields.release_notes'))
                    ->rows(4)
                    ->maxLength(65535),
                SpatieMediaLibraryFileUpload::make('zip')
                    ->label(__('admin.resources.products.relation_releases.fields.zip'))
                    ->collection('zip')
                    ->acceptedFileTypes(['application/zip', 'application/x-zip-compressed'])
                    ->maxSize(102400)
                    ->downloadable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('version')
            ->columns([
                TextColumn::make('version')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('zip_file_name')
                    ->label(__('admin.resources.products.relation_releases.columns.zip_file'))
                    ->state(fn ($record): ?string => $record->getZipFile()?->file_name)
                    ->placeholder(__('admin.common.na'))
                    ->searchable(query: function ($query, string $search): void {
                        $query->whereHas('media', function ($mediaQuery) use ($search): void {
                            $mediaQuery
                                ->where('collection_name', 'zip')
                                ->where('file_name', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('created_at')
                    ->label(__('admin.common.created'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon(Heroicon::OutlinedPlus),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading(__('admin.resources.products.relation_releases.empty.heading'))
            ->emptyStateDescription(__('admin.resources.products.relation_releases.empty.description'))
            ->emptyStateIcon(Heroicon::OutlinedArrowDownTray);
    }
}
