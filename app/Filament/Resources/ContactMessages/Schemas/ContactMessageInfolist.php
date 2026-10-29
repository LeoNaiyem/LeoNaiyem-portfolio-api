<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ContactMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->copyable(),
                TextEntry::make('created_at')
                    ->label('Received')
                    ->dateTime(),
                TextEntry::make('ip')
                    ->label('IP address')
                    ->placeholder('Unknown'),
                TextEntry::make('message')
                    ->columnSpanFull()
                    ->prose(),
            ]);
    }
}
