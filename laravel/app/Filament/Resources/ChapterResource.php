<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChapterResource\Pages;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\GradeLevel;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class ChapterResource extends Resource
{
    protected static ?string $model = Chapter::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-book-open';

    protected static UnitEnum|string|null $navigationGroup = 'Content';

    public static function form(Schema $schema): Schema
    {
        $components = [
            Forms\Components\Select::make('course_id')->relationship('course', 'slug')->searchable()->required(),
            Forms\Components\TextInput::make('slug')->required()->maxLength(150),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_published')->default(true),
        ];

        if (\Illuminate\Support\Facades\Schema::hasTable('grade_levels')) {
            $components[] = Forms\Components\CheckboxList::make('gradeLevels')
                ->label('Recommended grade levels')
                ->relationship(
                    'gradeLevels',
                    'title_en',
                    fn ($query) => $query->where('is_active', true)->orderBy('sort_order')->orderBy('grade_number')
                )
                ->getOptionLabelFromRecordUsing(fn (GradeLevel $record): string => $record->title_en.' / '.$record->title_ru)
                ->columns(4)
                ->columnSpanFull();
        }

        return $schema->schema($components);
    }

    public static function table(Table $table): Table
    {
        $columns = [
            Tables\Columns\TextColumn::make('course.slug')->searchable(),
            Tables\Columns\TextColumn::make('slug')->searchable()->sortable(),
        ];

        if (\Illuminate\Support\Facades\Schema::hasTable('grade_levels')) {
            $columns[] = Tables\Columns\TextColumn::make('gradeLevels.grade_number')->label('Grades')->badge();
        }

        $columns = [
            ...$columns,
            Tables\Columns\TextColumn::make('sort_order')->sortable(),
            Tables\Columns\IconColumn::make('is_published')->boolean(),
            Tables\Columns\TextColumn::make('problems_count')->counts('problems')->label('Problems'),
        ];

        return $table
            ->defaultSort('sort_order')
            ->columns($columns)
            ->filters([
                Tables\Filters\SelectFilter::make('course_id')->options(fn () => Course::query()->pluck('slug', 'id')->all()),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChapters::route('/'),
            'create' => Pages\CreateChapter::route('/create'),
            'edit' => Pages\EditChapter::route('/{record}/edit'),
        ];
    }
}


