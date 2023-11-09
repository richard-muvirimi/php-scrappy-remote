<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('option_metas')) {
            Schema::create('option_metas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('option')->index()->constrained()->onUpdate('cascade')->onDelete('cascade');
                $table->string('key', 50)->index();
                $table->string('value');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('option_metas');
    }
};
