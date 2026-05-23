<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagTextResource\Pages;
use App\Models\TagText;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use BackedEnum;
use UnitEnum;

class TagTextResource extends Resource
{
    protected static ?string $model = TagText::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static UnitEnum|string|null $navigationGroup = 'Taxonomy';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('tag_id')->relationship('tag', 'slug')->searchable()->required(),
            Forms\Components\TextInput::make('lang')->required()->maxLength(10),
            Forms\Components\TextInput::make('title')->required()->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id')
            ->columns([
                Tables\Columns\TextColumn::make('tag.slug')->searchable(),
                Tables\Columns\TextColumn::make('lang')->badge(),
                Tables\Columns\TextColumn::make('title')->searchable(),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTagTexts::route('/'),
            'create' => Pages\CreateTagText::route('/create'),
            'edit' => Pages\EditTagText::route('/{record}/edit'),
        ];
    }
}


