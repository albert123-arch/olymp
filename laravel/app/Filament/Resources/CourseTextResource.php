<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseTextResource\Pages;
use App\Models\Course;
use App\Models\CourseText;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use BackedEnum;
use UnitEnum;

class CourseTextResource extends Resource
{
    protected static ?string $model = CourseText::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static UnitEnum|string|null $navigationGroup = 'Translations';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('course_id')->relationship('course', 'slug')->searchable()->required(),
            Forms\Components\TextInput::make('lang')->required()->maxLength(10),
            Forms\Components\TextInput::make('title')->required()->maxLength(255),
            Forms\Components\Textarea::make('description_html')->rows(10)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id')
            ->columns([
                Tables\Columns\TextColumn::make('course.slug')->searchable(),
                Tables\Columns\TextColumn::make('lang')->badge(),
                Tables\Columns\TextColumn::make('title')->searchable()->wrap(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('course_id')->options(fn () => Course::query()->pluck('slug', 'id')->all()),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourseTexts::route('/'),
            'create' => Pages\CreateCourseText::route('/create'),
            'edit' => Pages\EditCourseText::route('/{record}/edit'),
        ];
    }
}


