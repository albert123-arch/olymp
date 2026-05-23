<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChapterTextResource\Pages;
use App\Models\Chapter;
use App\Models\ChapterText;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use BackedEnum;
use UnitEnum;

class ChapterTextResource extends Resource
{
    protected static ?string $model = ChapterText::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static UnitEnum|string|null $navigationGroup = 'Translations';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('chapter_id')->relationship('chapter', 'slug')->searchable()->required(),
            Forms\Components\TextInput::make('lang')->required()->maxLength(10),
            Forms\Components\TextInput::make('title')->required()->maxLength(255),
            Forms\Components\Textarea::make('description_html')->rows(5)->columnSpanFull(),
            Forms\Components\Textarea::make('theory_html')->rows(14)->columnSpanFull(),
            Forms\Components\Textarea::make('examples_html')->rows(14)->columnSpanFull(),
            Forms\Components\Textarea::make('worksheet_html')->rows(10)->columnSpanFull(),
            Forms\Components\Textarea::make('teacher_notes_html')->rows(10)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id')
            ->columns([
                Tables\Columns\TextColumn::make('chapter.slug')->searchable(),
                Tables\Columns\TextColumn::make('lang')->badge(),
                Tables\Columns\TextColumn::make('title')->searchable()->wrap(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('chapter_id')->options(fn () => Chapter::query()->pluck('slug', 'id')->all()),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChapterTexts::route('/'),
            'create' => Pages\CreateChapterText::route('/create'),
            'edit' => Pages\EditChapterText::route('/{record}/edit'),
        ];
    }
}


