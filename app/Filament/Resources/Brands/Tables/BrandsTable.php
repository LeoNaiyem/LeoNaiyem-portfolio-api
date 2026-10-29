<?php

namespace App\Filament\Resources\Brands\Tables;

use App\Filament\Support\SortableTable;
use App\Support\Media;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BrandsTable
{
    public static function configure(Table $table): Table
    {
        return SortableTable::configure($table)
            ->columns([
                ImageColumn::make('image')
                    ->label('Logo')
                    ->disk(Media::disk()),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('url')
                    ->toggleable(),
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
