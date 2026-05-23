<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Language::query()->firstOrCreate(
            ['code' => 'ru'],
            ['title' => 'Русский', 'is_default' => true, 'is_active' => true, 'sort_order' => 1],
        );

        Language::query()->firstOrCreate(
            ['code' => 'en'],
            ['title' => 'English', 'is_default' => false, 'is_active' => true, 'sort_order' => 2],
        );
    }
}
