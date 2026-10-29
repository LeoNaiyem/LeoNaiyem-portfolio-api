<?php

namespace App\Filament\Resources\Brands\Schemas;

use App\Support\Media;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('url')
                    ->url(),
                FileUpload::make('image')
                    ->label('Logo')
                    ->image()
                    ->disk(Media::disk())
                    ->directory('brands')
                    ->visibility('public')
                    ->maxSize(2048),
            ]);
    }
}
