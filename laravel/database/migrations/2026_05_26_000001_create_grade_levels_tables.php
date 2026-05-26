<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('grade_levels')) {
            Schema::create('grade_levels', function (Blueprint $table): void {
                $table->increments('id');
                $table->unsignedTinyInteger('grade_number')->unique();
                $table->string('title_ru', 100);
                $table->string('title_en', 100);
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('problem_grade_levels')) {
            Schema::create('problem_grade_levels', function (Blueprint $table): void {
                $table->increments('id');
                $table->unsignedInteger('problem_id')->index();
                $table->unsignedInteger('grade_level_id')->index();
                $table->boolean('is_primary')->default(false);
                $table->timestamps();
                $table->unique(['problem_id', 'grade_level_id'], 'problem_grade_unique');
            });
        }

        if (! Schema::hasTable('problem_ladder_grade_levels')) {
            Schema::create('problem_ladder_grade_levels', function (Blueprint $table): void {
                $table->increments('id');
                $table->unsignedInteger('problem_ladder_id')->index('ladder_grade_ladder_idx');
                $table->unsignedInteger('grade_level_id')->index('ladder_grade_level_idx');
                $table->boolean('is_primary')->default(false);
                $table->timestamps();
                $table->unique(['problem_ladder_id', 'grade_level_id'], 'ladder_grade_unique');
            });
        }

        if (! Schema::hasTable('chapter_grade_levels')) {
            Schema::create('chapter_grade_levels', function (Blueprint $table): void {
                $table->increments('id');
                $table->unsignedInteger('chapter_id')->index();
                $table->unsignedInteger('grade_level_id')->index();
                $table->boolean('is_primary')->default(false);
                $table->timestamps();
                $table->unique(['chapter_id', 'grade_level_id'], 'chapter_grade_unique');
            });
        }

        foreach ([5, 6, 7, 8, 9, 10, 11] as $grade) {
            DB::table('grade_levels')->updateOrInsert(
                ['grade_number' => $grade],
                [
                    'title_ru' => $grade.' '."\u{043A}\u{043B}\u{0430}\u{0441}\u{0441}",
                    'title_en' => 'Grade '.$grade,
                    'sort_order' => $grade,
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }
    }

    public function down(): void
    {
        // Intentionally left empty: this production migration must not drop data.
    }
};
