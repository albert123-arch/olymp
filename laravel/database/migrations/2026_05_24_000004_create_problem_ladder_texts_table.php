<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('problem_ladder_texts')) {
            return;
        }

        Schema::create('problem_ladder_texts', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('problem_ladder_id');
            $table->unsignedInteger('language_id');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('main_method', 255)->nullable();
            $table->timestamps();

            $table->unique(['problem_ladder_id', 'language_id'], 'problem_ladder_texts_ladder_lang_unique');
            $table->index('problem_ladder_id', 'problem_ladder_texts_ladder_idx');
            $table->index('language_id', 'problem_ladder_texts_language_idx');

            $table->foreign('problem_ladder_id', 'problem_ladder_texts_ladder_fk')
                ->references('id')
                ->on('problem_ladders')
                ->cascadeOnDelete();
            $table->foreign('language_id', 'problem_ladder_texts_language_fk')
                ->references('id')
                ->on('languages')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('problem_ladder_texts');
    }
};

