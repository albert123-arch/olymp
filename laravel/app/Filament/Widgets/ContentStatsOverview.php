<?php

namespace App\Filament\Widgets;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\Problem;
use App\Models\ProblemLadder;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Courses', Course::query()->count()),
            Stat::make('Chapters', Chapter::query()->count()),
            Stat::make('Problems', Problem::query()->count()),
            Stat::make('Ladders', ProblemLadder::query()->count()),
        ];
    }
}
