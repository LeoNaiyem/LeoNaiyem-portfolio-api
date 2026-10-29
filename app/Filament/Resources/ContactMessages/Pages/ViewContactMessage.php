<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if (! $this->record->read_at) {
            $this->record->update(['read_at' => now()]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reply')
                ->icon(Heroicon::OutlinedEnvelope)
                ->url(fn () => 'mailto:'.$this->record->email.'?subject='.rawurlencode('Re: your message')),
            Action::make('markUnread')
                ->label('Mark as unread')
                ->color('gray')
                ->action(function () {
                    $this->record->update(['read_at' => null]);
                    $this->redirect(ContactMessageResource::getUrl());
                }),
            DeleteAction::make(),
        ];
    }
}
