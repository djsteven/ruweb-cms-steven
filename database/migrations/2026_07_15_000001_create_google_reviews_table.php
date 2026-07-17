<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_reviews', function (Blueprint $table) {
            $table->id();

            // Datos del negocio (para soportar múltiples Place IDs en el futuro)
            $table->string('place_id', 255)->index();

            // Datos del autor
            $table->string('author_name', 255);
            $table->string('author_url', 500)->nullable();
            $table->string('profile_photo_url', 500)->nullable();

            // Datos de la reseña
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->text('text')->nullable();
            $table->string('relative_time_description', 100)->nullable();
            $table->timestamp('review_time')->nullable()->index();

            // Control editorial
            $table->boolean('is_visible')->default(false)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();

            // Metadatos de importación
            $table->timestamp('imported_at')->nullable();

            $table->timestamps();

            // Evita duplicados: mismo autor + mismo tiempo en el mismo lugar
            $table->unique(['place_id', 'author_name', 'review_time'], 'google_reviews_unique_review');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_reviews');
    }
};
