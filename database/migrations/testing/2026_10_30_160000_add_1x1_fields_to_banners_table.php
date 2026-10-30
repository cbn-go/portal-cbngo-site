<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 * Espelho do schema do painel administrativo (cbn-go/portal-cbngo-admin), usado apenas
 * nos ambientes local e testing. Em produção, o portal lê o banco do painel e não migra nada.
 *
 * Origem: database/migrations/2026_10_30_160000_add_1x1_fields_to_banners_table.php
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('tag', 30)->nullable()->after('title');
            $table->string('subtitle', 160)->nullable()->after('tag');
            $table->string('meta', 60)->nullable()->after('subtitle');
            $table->boolean('is_square')->default(true)->after('is_active');
        });

        // Banners existentes no formato antigo 16:9 são marcados como legados e inativos
        DB::table('banners')->update([
            'is_square' => false,
            'is_active' => false,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['tag', 'subtitle', 'meta', 'is_square']);
        });
    }
};
