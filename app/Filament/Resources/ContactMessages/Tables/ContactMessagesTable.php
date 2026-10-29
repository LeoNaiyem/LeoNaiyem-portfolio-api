<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->weight(fn (ContactMessage $record) => $record->read_at ? null : 'bold'),
                TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('message')
                    ->limit(60),
                TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('read_at')
                    ->label('Read')
                    ->nullable(),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('reply')
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->url(fn (ContactMessage $record) => 'mailto:'.$record->email.'?subject='.rawurlencode('Re: your message')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
