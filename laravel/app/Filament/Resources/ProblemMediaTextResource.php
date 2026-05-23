<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProblemMediaTextResource\Pages;
use App\Models\ProblemMediaText;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use BackedEnum;
use UnitEnum;

class ProblemMediaTextResource extends Resource
{
    protected static ?string $model = ProblemMediaText::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static UnitEnum|string|null $navigationGroup = 'Problems';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('media_id')
                ->relationship('media', 'original_name')
                ->searchable()
                ->required(),
            Forms\Components\TextInput::make('lang')->required()->maxLength(10),
            Forms\Components\Textarea::make('caption_html')->rows(8)->columnSpanFull(),
            Forms\Components\TextInput::make('alt_text')->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id')
            ->columns([
                Tables\Columns\TextColumn::make('media.original_name')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('lang')->badge(),
                Tables\Columns\TextColumn::make('alt_text')->searchable()->wrap(),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProblemMediaTexts::route('/'),
            'create' => Pages\CreateProblemMediaText::route('/create'),
            'edit' => Pages\EditProblemMediaText::route('/{record}/edit'),
        ];
    }
}


