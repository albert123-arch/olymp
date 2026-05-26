<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('problems')) {
            return;
        }

        Schema::table('problems', function (Blueprint $table): void {
            if (! Schema::hasColumn('problems', 'source_name')) {
                $table->string('source_name', 255)->nullable()->after('is_published');
            }
            if (! Schema::hasColumn('problems', 'source_year')) {
                $table->unsignedSmallInteger('source_year')->nullable()->after('source_name');
            }
            if (! Schema::hasColumn('problems', 'source_round')) {
                $table->string('source_round', 100)->nullable()->after('source_year');
            }
            if (! Schema::hasColumn('problems', 'source_grade')) {
                $table->string('source_grade', 50)->nullable()->after('source_round');
            }
            if (! Schema::hasColumn('problems', 'source_problem_number')) {
                $table->string('source_problem_number', 50)->nullable()->after('source_grade');
            }
            if (! Schema::hasColumn('problems', 'source_url')) {
                $table->string('source_url', 500)->nullable()->after('source_problem_number');
            }
            if (! Schema::hasColumn('problems', 'source_note')) {
                $table->text('source_note')->nullable()->after('source_url');
            }
        });
    }

    public function down(): void
    {
        // Intentionally left empty: this production migration must not drop data.
    }
};
