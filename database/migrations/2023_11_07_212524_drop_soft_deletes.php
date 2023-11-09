<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $tables = [
        'users',
        'user_metas',
        'content_metas',
        'contents',
        'options',
        'option_metas',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Collection::make($this->tables)->each(function (string $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Collection::make($this->tables)->each(function (string $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->softDeletes()->nullable()->default(null);
            });
        });
    }
};
