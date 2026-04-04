<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mcp_api_key_hash', 64)->nullable()->index()->after('remember_token');
            $table->timestamp('mcp_api_key_generated_at')->nullable()->after('mcp_api_key_hash');
            $table->timestamp('mcp_api_key_last_used_at')->nullable()->after('mcp_api_key_generated_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'mcp_api_key_hash',
                'mcp_api_key_generated_at',
                'mcp_api_key_last_used_at',
            ]);
        });
    }
};
