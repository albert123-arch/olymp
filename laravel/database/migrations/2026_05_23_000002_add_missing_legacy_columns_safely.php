<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('problems')) {
            Schema::table('problems', function (Blueprint $table): void {
                if (! Schema::hasColumn('problems', 'sort_order')) {
                    $table->integer('sort_order')->default(0)->after('problem_type');
                }

                if (! Schema::hasColumn('problems', 'is_published')) {
                    $table->boolean('is_published')->default(true)->after('sort_order');
                }
            });
        }

        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table): void {
                if (! Schema::hasColumn('courses', 'sort_order')) {
                    $table->integer('sort_order')->default(0)->after('slug');
                }

                if (! Schema::hasColumn('courses', 'is_published')) {
                    $table->boolean('is_published')->default(true)->after('sort_order');
                }
            });
        }

        if (Schema::hasTable('chapters')) {
            Schema::table('chapters', function (Blueprint $table): void {
                if (! Schema::hasColumn('chapters', 'sort_order')) {
                    $table->integer('sort_order')->default(0)->after('slug');
                }

                if (! Schema::hasColumn('chapters', 'is_published')) {
                    $table->boolean('is_published')->default(true)->after('sort_order');
                }
            });
        }
    }

    public function down(): void
    {
        // Intentionally no-op: these columns may belong to the legacy schema and must not be dropped.
    }
};
