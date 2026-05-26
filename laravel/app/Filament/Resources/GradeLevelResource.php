<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GradeLevelResource\Pages;
use App\Models\GradeLevel;
use BackedEnum;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class GradeLevelResource extends Resource
{
    protected static ?string $model = GradeLevel::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static UnitEnum|string|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Grade Levels';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('grade_number')
                ->numeric()
                ->minValue(1)
                ->maxValue(12)
                ->required()
                ->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('title_ru')->required()->maxLength(100),
            Forms\Components\TextInput::make('title_en')->required()->maxLength(100),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('grade_number')->sortable()->badge(),
                Tables\Columns\TextColumn::make('title_ru')->searchable(),
                Tables\Columns\TextColumn::make('title_en')->searchable(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGradeLevels::route('/'),
            'create' => Pages\CreateGradeLevel::route('/create'),
            'edit' => Pages\EditGradeLevel::route('/{record}/edit'),
        ];
    }
}
