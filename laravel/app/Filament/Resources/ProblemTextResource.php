<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProblemTextResource\Pages;
use App\Models\Problem;
use App\Models\ProblemText;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use BackedEnum;
use UnitEnum;

class ProblemTextResource extends Resource
{
    protected static ?string $model = ProblemText::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static UnitEnum|string|null $navigationGroup = 'Problems';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('problem_id')
                ->relationship('problem', 'problem_code')
                ->searchable()
                ->required(),
            Forms\Components\TextInput::make('lang')->required()->maxLength(10),
            Forms\Components\TextInput::make('title')->required()->maxLength(255),
            Forms\Components\Textarea::make('statement_html')->rows(12)->required()->columnSpanFull(),
            Forms\Components\Textarea::make('hint_html')->rows(8)->columnSpanFull(),
            Forms\Components\Textarea::make('solution_html')->rows(12)->columnSpanFull(),
            Forms\Components\Textarea::make('teacher_note_html')->rows(8)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id')
            ->columns([
                Tables\Columns\TextColumn::make('problem.problem_code')->searchable(),
                Tables\Columns\TextColumn::make('lang')->badge(),
                Tables\Columns\TextColumn::make('title')->searchable()->wrap(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('problem_id')->options(fn () => Problem::query()->pluck('problem_code', 'id')->all()),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProblemTexts::route('/'),
            'create' => Pages\CreateProblemText::route('/create'),
            'edit' => Pages\EditProblemText::route('/{record}/edit'),
        ];
    }
}


