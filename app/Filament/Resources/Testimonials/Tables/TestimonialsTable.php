<?php

namespace App\Filament\Resources\Testimonials\Tables;

use App\Filament\Support\SortableTable;
use App\Support\Media;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return SortableTable::configure($table)
            ->columns([
                ImageColumn::make('image')
                    ->disk(Media::disk())
                    ->circular(),
                TextColumn::make('name')
                    ->searchable()
                    ->description(fn ($record) => $record->company),
                TextColumn::make('feedback')
                    ->limit(80),
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
