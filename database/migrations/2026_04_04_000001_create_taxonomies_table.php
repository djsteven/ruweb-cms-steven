<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxonomies', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 10)->default(config('cms.locales.default', 'es'))->index();
            $table->uuid('translation_group_id')->nullable()->index();
            $table->string('translation_status', 30)->nullable()->index();
            $table->string('source_fingerprint', 64)->nullable();
            $table->json('source_field_hashes')->nullable();
            $table->string('name');
            $table->string('slug');
            $table->string('type'); // e.g. 'category', 'tag'
            $table->foreignId('parent_id')->nullable()->constrained('taxonomies')->nullOnDelete();
            $table->text('description')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->unique(['locale', 'slug', 'type']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxonomies');
    }
};
