<?php

namespace App\Filament\Resources\ProblemResource\RelationManagers;

use App\Support\ProblemMediaUpload;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class MediaRelationManager extends RelationManager
{
    protected static string $relationship = 'media';

    protected static ?string $title = 'Problem media';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('role')
                ->options(ProblemMediaUpload::roleLabels())
                ->required(),
            Forms\Components\Select::make('lang')
                ->options(['' => 'Any', 'ru' => 'RU', 'en' => 'EN'])
                ->nullable(),
            Forms\Components\TextInput::make('file_path')
                ->helperText('Use /uploads/problems/{problem_id}/filename.svg. Upload files through Problem Builder or Bulk Diagrams.')
                ->required()
                ->maxLength(500)
                ->columnSpanFull(),
            Forms\Components\TextInput::make('original_name')
                ->maxLength(255),
            Forms\Components\TextInput::make('mime_type')
                ->maxLength(100),
            Forms\Components\TextInput::make('file_size')
                ->numeric(),
            Forms\Components\TextInput::make('sort_order')
                ->numeric()
                ->default(0),
            Forms\Components\Toggle::make('is_published')
                ->default(true),
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
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lang')
                    ->placeholder('Any')
                    ->badge(),
                Tables\Columns\TextColumn::make('original_name')
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('file_path')
                    ->wrap()
                    ->copyable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(fn ($record) => ProblemMediaUpload::deleteMediaFileIfSafe($record)),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
