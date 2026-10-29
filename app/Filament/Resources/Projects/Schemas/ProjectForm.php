<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Support\Media;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Project')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state, string $operation) {
                                if ($operation === 'create' || blank($get('slug'))) {
                                    $set('slug', Str::slug((string) $state));
                                }
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->alphaDash()
                            ->unique(ignoreRecord: true)
                            ->helperText('Used in the URL: /projects/{slug}'),
                        Textarea::make('description')
                            ->required()
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Short summary shown on the project card and in search results.')
                            ->columnSpanFull(),
                        MarkdownEditor::make('body')
                            ->helperText('Full write-up for the project page (Markdown).')
                            ->columnSpanFull(),
                        TextInput::make('project_url')
                            ->label('Live URL')
                            ->url(),
                        TextInput::make('code_url')
                            ->label('Source code URL')
                            ->url(),
                    ]),
                Section::make('Publishing')
                    ->columnSpan(1)
                    ->schema([
                        FileUpload::make('image')
                            ->image()
                            ->imageEditor()
                            ->disk(Media::disk())
                            ->directory('projects')
                            ->visibility('public')
                            ->maxSize(4096),
                        Select::make('tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug((string) $state))),
                                TextInput::make('slug')
                                    ->required()
                                    ->unique('tags', 'slug'),
                            ]),
                        DateTimePicker::make('published_at')
                            ->label('Publish date')
                            ->helperText('Leave empty to keep as a draft.'),
                        Toggle::make('is_featured'),
                    ]),
            ]);
    }
}
