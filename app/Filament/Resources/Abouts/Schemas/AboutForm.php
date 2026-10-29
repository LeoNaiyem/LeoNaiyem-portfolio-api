<?php

namespace App\Filament\Resources\Abouts\Schemas;

use App\Support\Media;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AboutForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('image')
                    ->image()
                    ->disk(Media::disk())
                    ->directory('abouts')
                    ->visibility('public')
                    ->maxSize(4096),
                Textarea::make('description')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
