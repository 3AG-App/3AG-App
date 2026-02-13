<?php

namespace App\Filament\Resources\Licenses\Pages;

use App\Enums\LicenseStatus;
use App\Filament\Resources\Licenses\LicenseResource;
use App\Models\License;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewLicense extends ViewRecord
{
    protected static string $resource = LicenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('copyLicenseKey')
                ->label(__('admin.resources.licenses.view.actions.copy_key'))
                ->icon(Heroicon::Clipboard)
                ->color('gray')
                ->action(function () {
                    Notification::make()
                        ->title(__('admin.resources.licenses.view.notifications.copied'))
                        ->success()
                        ->send();
                })
                ->extraAttributes([
                    'x-data' => '',
                    'x-on:click' => 'navigator.clipboard.writeText(\''.$this->record->license_key.'\')',
                ]),
            Action::make('suspend')
                ->label(__('admin.resources.licenses.view.actions.suspend'))
                ->icon(Heroicon::PauseCircle)
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(__('admin.resources.licenses.view.modals.suspend.heading'))
                ->modalDescription(__('admin.resources.licenses.view.modals.suspend.description'))
                ->action(function (License $record) {
                    $record->update(['status' => LicenseStatus::Suspended]);
                    Notification::make()
                        ->title(__('admin.resources.licenses.view.notifications.suspended'))
                        ->success()
                        ->send();
                })
                ->visible(fn () => $this->record->status === LicenseStatus::Active),
            Action::make('activate')
                ->label(__('admin.resources.licenses.view.actions.activate'))
                ->icon(Heroicon::CheckCircle)
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading(__('admin.resources.licenses.view.modals.activate.heading'))
                ->modalDescription(__('admin.resources.licenses.view.modals.activate.description'))
                ->action(function (License $record) {
                    $record->update(['status' => LicenseStatus::Active]);
                    Notification::make()
                        ->title(__('admin.resources.licenses.view.notifications.activated'))
                        ->success()
                        ->send();
                })
                ->visible(fn () => $this->record->status !== LicenseStatus::Active),
            EditAction::make()
                ->icon(Heroicon::Pencil),
            DeleteAction::make()
                ->icon(Heroicon::Trash),
        ];
    }
}
