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
        if (! Schema::hasTable('content_metas')) {
            Schema::create('content_metas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('content')->index()->constrained()->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('content_metas');
    }
};
