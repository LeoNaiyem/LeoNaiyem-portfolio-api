<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use App\Models\Setting;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class ManageSettings extends EditRecord
{
    protected static string $resource = SettingResource::class;

    public function mount(int|string|null $record = null): void
    {
        parent::mount($record ?? Setting::current()->getKey());
    }

    public function getTitle(): string|Htmlable
    {
        return 'Profile & SEO';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): ?string
    {
        return null;
    }
}
