<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProblemLadderResource\Pages;
use App\Filament\Resources\ProblemLadderResource\RelationManagers\LadderTextsRelationManager;
use App\Models\ProblemLadder;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use BackedEnum;
use UnitEnum;
use Illuminate\Support\Str;

class ProblemLadderResource extends Resource
{
    protected static ?string $model = ProblemLadder::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-list-bullet';

    protected static UnitEnum|string|null $navigationGroup = 'Ladders';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('course_id')
                ->relationship('course', 'slug')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\Select::make('chapter_id')
                ->relationship('chapter', 'slug')
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('title')->required()->maxLength(255)->live(onBlur: true)
                ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(255),
            Forms\Components\Textarea::make('description')->rows(5)->columnSpanFull(),
            Forms\Components\TextInput::make('main_method')->maxLength(255),
            Forms\Components\TextInput::make('difficulty_level')->numeric()->minValue(1)->maxValue(5)->default(1),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_published')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('course.slug')->searchable(),
                Tables\Columns\TextColumn::make('chapter.slug')->searchable(),
                Tables\Columns\TextColumn::make('main_method')->searchable(),
                Tables\Columns\TextColumn::make('difficulty_level')->badge(),
                Tables\Columns\TextColumn::make('steps_count')->counts('steps')->label('Steps'),
                Tables\Columns\IconColumn::make('is_published')->boolean(),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProblemLadders::route('/'),
            'create' => Pages\CreateProblemLadder::route('/create'),
            'edit' => Pages\EditProblemLadder::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            LadderTextsRelationManager::class,
        ];
    }
}


