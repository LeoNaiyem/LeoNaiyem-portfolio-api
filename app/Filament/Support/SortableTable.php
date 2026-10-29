<?php

namespace App\Filament\Support;

use App\Jobs\RevalidateFrontend;
use Filament\Tables\Table;

class SortableTable
{
    /**
     * Drag-and-drop ordering on `sort_order`.
     *
     * Filament reorders with a bulk query that skips model events, so the
     * frontend is revalidated here instead.
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->afterReordering(function () use ($table) {
                foreach (app($table->getModel())->revalidationTags() as $tag) {
                    RevalidateFrontend::dispatch($tag);
                }
            });
    }
}
