<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->unsignedInteger('width')->nullable()->after('size');
            $table->unsignedInteger('height')->nullable()->after('width');
            $table->json('variants')->nullable()->after('height');
            $table->unsignedBigInteger('original_size')->nullable()->after('variants');
            $table->string('original_extension', 20)->nullable()->after('original_size');
            $table->string('original_mime_type', 100)->nullable()->after('original_extension');
            $table->string('original_path')->nullable()->after('original_mime_type');
            $table->timestamp('optimized_at')->nullable()->after('original_path');
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn([
                'width',
                'height',
                'variants',
                'original_size',
                'original_extension',
                'original_mime_type',
                'original_path',
                'optimized_at',
            ]);
        });
    }
};

