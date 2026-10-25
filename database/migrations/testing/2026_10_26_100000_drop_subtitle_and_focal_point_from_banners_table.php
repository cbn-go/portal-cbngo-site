<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Espelho do schema do painel administrativo (cbn-go/portal-cbngo-admin), usado apenas
 * nos ambientes local e testing. Em produção, o portal lê o banco do painel e não migra nada.
 *
 * Origem: database/migrations/2026_10_26_100000_drop_subtitle_and_focal_point_from_banners_table.php
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['subtitle', 'focal_point']);
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->string('alt_text', 500)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('alt_text', 255)->change();
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->string('subtitle', 255)->nullable()->after('title');
            $table->string('focal_point', 10)->default('center')->after('image');
        });
    }
};
