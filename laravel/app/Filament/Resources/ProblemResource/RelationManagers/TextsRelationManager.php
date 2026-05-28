<?php

namespace App\Filament\Resources\ProblemResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TextsRelationManager extends RelationManager
{
    protected static string $relationship = 'texts';

    protected static ?string $title = 'Problem texts';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('lang')
                ->options(['ru' => 'RU', 'en' => 'EN'])
                ->required(),
            Forms\Components\TextInput::make('title')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            Forms\Components\Textarea::make('statement_html')
                ->rows(8)
                ->columnSpanFull()
                ->required(),
            Forms\Components\Textarea::make('hint_html')
                ->rows(5)
                ->columnSpanFull(),
            Forms\Components\Textarea::make('solution_html')
                ->rows(8)
                ->columnSpanFull(),
            Forms\Components\Textarea::make('teacher_note_html')
                ->rows(5)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lang')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\IconColumn::make('statement_html')
                    ->label('Statement')
                    ->boolean()
                    ->getStateUsing(fn ($record): bool => filled($record->statement_html)),
                Tables\Columns\IconColumn::make('solution_html')
                    ->label('Solution')
                    ->boolean()
                    ->getStateUsing(fn ($record): bool => filled($record->solution_html)),
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
