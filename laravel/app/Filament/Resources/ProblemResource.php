<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProblemResource\Pages;
use App\Models\Chapter;
use App\Models\GradeLevel;
use App\Models\Problem;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use BackedEnum;
use UnitEnum;

class ProblemResource extends Resource
{
    protected static ?string $model = Problem::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static UnitEnum|string|null $navigationGroup = 'Problems';

    public static function form(Schema $schema): Schema
    {
        $components = [
            Forms\Components\Select::make('chapter_id')->relationship('chapter', 'slug')->searchable()->required(),
            Forms\Components\TextInput::make('problem_code')->required()->maxLength(50),
            Forms\Components\TextInput::make('book_number')->numeric(),
            Forms\Components\TextInput::make('difficulty')->numeric()->minValue(1)->maxValue(5)->default(1),
            Forms\Components\Select::make('problem_type')->options([
                'computation' => 'Computation',
                'proof' => 'Proof',
                'counterexample' => 'Counterexample',
                'construction' => 'Construction',
                'challenge' => 'Challenge',
                'mixed' => 'Mixed',
            ])->required(),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_published')->default(true),
        ];

        if (\Illuminate\Support\Facades\Schema::hasTable('grade_levels')) {
            $components[] = Forms\Components\CheckboxList::make('gradeLevels')
                ->label('Grade levels')
                ->relationship(
                    'gradeLevels',
                    'title_en',
                    fn ($query) => $query->where('is_active', true)->orderBy('sort_order')->orderBy('grade_number')
                )
                ->getOptionLabelFromRecordUsing(fn (GradeLevel $record): string => $record->title_en.' / '.$record->title_ru)
                ->columns(4)
                ->columnSpanFull();
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('problems', 'source_name')) {
            $components[] = Forms\Components\Section::make('Source / Olympiad metadata')
                ->schema([
                    Forms\Components\TextInput::make('source_name')->maxLength(255),
                    Forms\Components\TextInput::make('source_year')->numeric()->minValue(1800)->maxValue(2200),
                    Forms\Components\TextInput::make('source_round')->maxLength(100),
                    Forms\Components\TextInput::make('source_grade')->maxLength(50),
                    Forms\Components\TextInput::make('source_problem_number')->maxLength(50),
                    Forms\Components\TextInput::make('source_url')->url()->maxLength(500),
                    Forms\Components\Textarea::make('source_note')->rows(4)->columnSpanFull(),
                ])
                ->columns(2)
                ->collapsible()
                ->collapsed()
                ->columnSpanFull();
        }

        return $schema->schema($components);
    }

    public static function table(Table $table): Table
    {
        $columns = [
            Tables\Columns\TextColumn::make('problem_code')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('chapter.slug')->searchable(),
            Tables\Columns\TextColumn::make('difficulty')->badge()->sortable(),
        ];

        if (\Illuminate\Support\Facades\Schema::hasTable('grade_levels')) {
            $columns[] = Tables\Columns\TextColumn::make('gradeLevels.grade_number')->label('Grades')->badge();
        }

        $columns = [
            ...$columns,
            Tables\Columns\TextColumn::make('problem_type')->badge(),
            Tables\Columns\TextColumn::make('source_compact')->label('Source')->toggleable()->wrap(),
            Tables\Columns\TextColumn::make('sort_order')->sortable(),
            Tables\Columns\IconColumn::make('is_published')->boolean(),
            Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
        ];

        $filters = [
            Tables\Filters\SelectFilter::make('chapter_id')->options(fn () => Chapter::query()->pluck('slug', 'id')->all()),
            Tables\Filters\SelectFilter::make('difficulty')->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5']),
        ];

        if (\Illuminate\Support\Facades\Schema::hasTable('grade_levels')) {
            $filters[] = Tables\Filters\SelectFilter::make('grade_level')
                ->label('Grade')
                ->relationship('gradeLevels', 'title_en')
                ->options(fn () => GradeLevel::query()->where('is_active', true)->orderBy('sort_order')->pluck('title_en', 'id')->all());
        }

        return $table
            ->defaultSort('sort_order')
            ->columns($columns)
            ->filters($filters)
            ->actions([EditAction::make()])
            ->bulkActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProblems::route('/'),
            'create' => Pages\CreateProblem::route('/create'),
            'edit' => Pages\EditProblem::route('/{record}/edit'),
        ];
    }
}


