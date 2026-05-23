<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProblemMediaResource\Pages;
use App\Models\ProblemMedia;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use BackedEnum;
use UnitEnum;

class ProblemMediaResource extends Resource
{
    protected static ?string $model = ProblemMedia::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-photo';

    protected static UnitEnum|string|null $navigationGroup = 'Problems';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('problem_id')->relationship('problem', 'problem_code')->searchable()->required(),
            Forms\Components\Select::make('role')->options([
                'statement' => 'Statement',
                'hint' => 'Hint',
                'solution' => 'Solution',
                'teacher_note' => 'Teacher Note',
                'extra' => 'Extra',
            ])->required(),
            Forms\Components\TextInput::make('lang')->maxLength(10),
            Forms\Components\TextInput::make('file_path')->required()->maxLength(500),
            Forms\Components\TextInput::make('original_name')->required()->maxLength(255),
            Forms\Components\TextInput::make('mime_type')->required()->maxLength(100),
            Forms\Components\TextInput::make('file_size')->numeric()->required(),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_published')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('problem.problem_code')->searchable(),
                Tables\Columns\TextColumn::make('role')->badge(),
                Tables\Columns\TextColumn::make('lang')->badge(),
                Tables\Columns\TextColumn::make('original_name')->searchable()->wrap(),
                Tables\Columns\IconColumn::make('is_published')->boolean(),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProblemMedia::route('/'),
            'create' => Pages\CreateProblemMedia::route('/create'),
            'edit' => Pages\EditProblemMedia::route('/{record}/edit'),
        ];
    }
}


