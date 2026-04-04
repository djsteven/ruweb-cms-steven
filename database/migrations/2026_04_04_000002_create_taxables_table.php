<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taxonomy_id')->constrained('taxonomies')->cascadeOnDelete();
            $table->morphs('taxable');
            $table->timestamps();

            $table->unique(['taxonomy_id', 'taxable_id', 'taxable_type'], 'taxables_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxables');
    }
};
