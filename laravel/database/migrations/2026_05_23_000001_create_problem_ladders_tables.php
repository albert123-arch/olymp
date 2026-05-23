<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('problem_ladders')) {
            Schema::create('problem_ladders', function (Blueprint $table): void {
                $table->increments('id');
                $table->unsignedInteger('course_id');
                $table->unsignedInteger('chapter_id')->nullable();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('main_method')->nullable();
                $table->unsignedTinyInteger('difficulty_level')->default(1);
                $table->integer('sort_order')->default(0);
                $table->boolean('is_published')->default(true);
                $table->timestamps();

                $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
                $table->foreign('chapter_id')->references('id')->on('chapters')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('problem_ladder_steps')) {
            Schema::create('problem_ladder_steps', function (Blueprint $table): void {
                $table->increments('id');
                $table->unsignedInteger('ladder_id');
                $table->unsignedBigInteger('problem_id');
                $table->string('step_label')->nullable();
                $table->string('step_title')->nullable();
                $table->enum('step_type', ['warmup', 'lemma', 'direct', 'mixed', 'target', 'challenge'])->default('warmup');
                $table->unsignedTinyInteger('difficulty_level')->default(1);
                $table->integer('sort_order')->default(0);
                $table->mediumText('hint_html')->nullable();
                $table->mediumText('teacher_note_html')->nullable();
                $table->timestamps();

                $table->unique(['ladder_id', 'problem_id']);
                $table->index(['ladder_id', 'sort_order']);
                $table->foreign('ladder_id')->references('id')->on('problem_ladders')->cascadeOnDelete();
                $table->foreign('problem_id')->references('id')->on('problems')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('problem_ladder_steps');
        Schema::dropIfExists('problem_ladders');
    }
};
