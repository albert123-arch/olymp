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

class LadderTextsRelationManager extends RelationManager
{
    protected static string $relationship = 'texts';

    protected static ?string $title = 'Translations';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('language_id')
                ->relationship('language', 'title')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\TextInput::make('title')
                ->required()
                ->maxLength(255),
            Forms\Components\Textarea::make('description')
                ->rows(5)
                ->columnSpanFull(),
            Forms\Components\TextInput::make('main_method')
                ->maxLength(255),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('language.code')
                    ->label('Lang')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('main_method')
                    ->wrap(),
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

