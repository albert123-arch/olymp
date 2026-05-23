<?php

namespace App\Filament\Widgets;

use App\Models\Problem;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentlyEditedProblems extends TableWidget
{
    protected static ?string $heading = 'Recently edited problems';

    protected int|string|array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Problem::query()->with('chapter')->latest('updated_at')->limit(8);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('problem_code')->label('Code')->searchable(),
                Tables\Columns\TextColumn::make('chapter.slug')->label('Chapter'),
                Tables\Columns\TextColumn::make('difficulty')->badge(),
                Tables\Columns\IconColumn::make('is_published')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime(),
            ]);
    }
}
