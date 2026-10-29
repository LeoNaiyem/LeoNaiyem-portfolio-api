<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use App\Support\Media;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('company')
                    ->maxLength(255),
                Textarea::make('feedback')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->image()
                    ->avatar()
                    ->disk(Media::disk())
                    ->directory('testimonials')
                    ->visibility('public')
                    ->maxSize(2048),
            ]);
    }
}
