<?php

namespace App\Filament\Resources\ProblemLadderResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class StepsRelationManager extends RelationManager
{
    protected static string $relationship = 'steps';

    protected static ?string $title = 'Steps';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('problem_id')
                ->relationship('problem', 'problem_code')
                ->searchable()
                ->required(),
            Forms\Components\Select::make('step_type')
                ->options([
                    'warmup' => 'Warmup',
                    'lemma' => 'Lemma',
                    'direct' => 'Direct',
                    'mixed' => 'Mixed',
                    'target' => 'Target',
                    'challenge' => 'Challenge',
                ])
                ->required()
                ->default('direct'),
            Forms\Components\TextInput::make('sort_order')
                ->numeric()
                ->default(0)
                ->required(),
            Forms\Components\TextInput::make('difficulty_level')
                ->numeric()
                ->minValue(1)
                ->maxValue(5)
                ->default(1)
                ->required(),
            Forms\Components\TextInput::make('step_label')
                ->maxLength(255),
            Forms\Components\TextInput::make('step_title')
                ->maxLength(255),
            Forms\Components\Textarea::make('hint_html')
                ->rows(5)
                ->columnSpanFull(),
            Forms\Components\Textarea::make('teacher_note_html')
                ->rows(5)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('problem.problem_code')
                    ->label('Problem')
                    ->searchable(),
                Tables\Columns\TextColumn::make('step_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('difficulty_level')
                    ->label('Level')
                    ->badge(),
                Tables\Columns\TextColumn::make('step_title')
                    ->wrap()
                    ->toggleable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
