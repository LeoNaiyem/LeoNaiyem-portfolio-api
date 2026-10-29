<?php

namespace App\Filament\Resources\Skills\Schemas;

use App\Support\Media;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SkillForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(100),
                ColorPicker::make('bg_color')
                    ->label('Background color'),
                FileUpload::make('icon')
                    ->image()
                    ->disk(Media::disk())
                    ->directory('skills')
                    ->visibility('public')
                    ->maxSize(1024),
            ]);
    }
}
