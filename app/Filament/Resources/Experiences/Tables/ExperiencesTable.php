<?php

namespace App\Filament\Resources\Experiences\Tables;

use App\Filament\Support\SortableTable;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExperiencesTable
{
    public static function configure(Table $table): Table
    {
        return SortableTable::configure($table)
            ->columns([
                TextColumn::make('year'),
                TextColumn::make('role')
                    ->searchable(),
                TextColumn::make('company')
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
