<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProblemLadderStepResource\Pages;
use App\Models\ProblemLadderStep;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use BackedEnum;
use UnitEnum;

class ProblemLadderStepResource extends Resource
{
    protected static ?string $model = ProblemLadderStep::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-list-bullet';

    protected static UnitEnum|string|null $navigationGroup = 'Ladders';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('ladder_id')
                ->relationship('ladder', 'title')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\Select::make('problem_id')
                ->relationship('problem', 'problem_code')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\TextInput::make('step_label')->maxLength(255),
            Forms\Components\TextInput::make('step_title')->maxLength(255),
            Forms\Components\Select::make('step_type')->options([
                'warmup' => 'Warmup',
                'lemma' => 'Lemma',
                'direct' => 'Direct',
                'mixed' => 'Mixed',
                'target' => 'Target',
                'challenge' => 'Challenge',
            ])->required(),
            Forms\Components\TextInput::make('difficulty_level')->numeric()->minValue(1)->maxValue(5)->default(1),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Textarea::make('hint_html')->rows(8)->columnSpanFull(),
            Forms\Components\Textarea::make('teacher_note_html')->rows(8)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('ladder.title')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('problem.problem_code')->searchable(),
                Tables\Columns\TextColumn::make('step_type')->badge(),
                Tables\Columns\TextColumn::make('difficulty_level')->badge(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProblemLadderSteps::route('/'),
            'create' => Pages\CreateProblemLadderStep::route('/create'),
            'edit' => Pages\EditProblemLadderStep::route('/{record}/edit'),
        ];
    }
}


