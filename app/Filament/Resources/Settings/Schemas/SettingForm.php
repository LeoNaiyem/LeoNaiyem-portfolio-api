<?php

namespace App\Filament\Resources\Settings\Schemas;

use App\Support\Media;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Profile')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('title')
                            ->placeholder('Full-stack developer')
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email(),
                        Textarea::make('bio')
                            ->rows(3)
                            ->columnSpanFull(),
                        FileUpload::make('avatar')
                            ->image()
                            ->disk(Media::disk())
                            ->directory('profile')
                            ->visibility('public')
                            ->maxSize(4096),
                        FileUpload::make('resume')
                            ->acceptedFileTypes(['application/pdf'])
                            ->disk(Media::disk())
                            ->directory('profile')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->maxSize(10240),
                    ]),
                Section::make('Social links')
                    ->columnSpan(1)
                    ->schema([
                        Repeater::make('socials')
                            ->hiddenLabel()
                            ->columns(2)
                            ->reorderable()
                            ->defaultItems(0)
                            ->schema([
                                Select::make('platform')
                                    ->required()
                                    ->options([
                                        'github' => 'GitHub',
                                        'linkedin' => 'LinkedIn',
                                        'twitter' => 'X / Twitter',
                                        'facebook' => 'Facebook',
                                        'instagram' => 'Instagram',
                                        'youtube' => 'YouTube',
                                        'website' => 'Website',
                                    ]),
                                TextInput::make('url')
                                    ->required()
                                    ->url(),
                            ]),
                    ]),
                Section::make('SEO defaults')
                    ->columnSpan(1)
                    ->schema([
                        TextInput::make('seo_title')
                            ->maxLength(70)
                            ->helperText('Up to about 60 characters is shown in Google.'),
                        Textarea::make('seo_description')
                            ->rows(3)
                            ->maxLength(320)
                            ->helperText('Up to about 155 characters is shown in Google.'),
                    ]),
            ]);
    }
}
