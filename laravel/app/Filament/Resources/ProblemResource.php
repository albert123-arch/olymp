<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProblemResource\Pages;
use App\Models\Chapter;
use App\Models\Problem;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use BackedEnum;
use UnitEnum;

class ProblemResource extends Resource
{
    protected static ?string $model = Problem::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static UnitEnum|string|null $navigationGroup = 'Problems';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('chapter_id')->relationship('chapter', 'slug')->searchable()->required(),
            Forms\Components\TextInput::make('problem_code')->required()->maxLength(50),
            Forms\Components\TextInput::make('book_number')->numeric(),
            Forms\Components\TextInput::make('difficulty')->numeric()->minValue(1)->maxValue(5)->default(1),
            Forms\Components\Select::make('problem_type')->options([
                'computation' => 'Computation',
                'proof' => 'Proof',
                'counterexample' => 'Counterexample',
                'construction' => 'Construction',
                'challenge' => 'Challenge',
                'mixed' => 'Mixed',
            ])->required(),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_published')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('problem_code')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('chapter.slug')->searchable(),
                Tables\Columns\TextColumn::make('difficulty')->badge()->sortable(),
                Tables\Columns\TextColumn::make('problem_type')->badge(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\IconColumn::make('is_published')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('chapter_id')->options(fn () => Chapter::query()->pluck('slug', 'id')->all()),
                Tables\Filters\SelectFilter::make('difficulty')->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5']),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProblems::route('/'),
            'create' => Pages\CreateProblem::route('/create'),
            'edit' => Pages\EditProblem::route('/{record}/edit'),
        ];
    }
}


