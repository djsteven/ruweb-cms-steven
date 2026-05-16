<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 10)->default(config('cms.locales.default', 'es'))->index();
            $table->string('name');
            $table->string('slug');
            $table->string('location')->nullable();
            $table->timestamps();

            $table->unique(['locale', 'slug']);
            $table->unique(['locale', 'location']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
