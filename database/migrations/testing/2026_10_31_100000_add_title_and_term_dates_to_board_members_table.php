<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Espelho do schema do painel administrativo (cbn-go/portal-cbngo-admin), usado apenas
 * nos ambientes local e testing. Em produção, o portal lê o banco do painel e não migra nada.
 *
 * Origem: database/migrations/2026_10_31_100000_add_title_and_term_dates_to_board_members_table.php
 * (sem a conversão dos dados existentes, que só existe no painel).
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('board_members', function (Blueprint $table) {
            $table->string('title', 20)->nullable()->after('name');
            $table->date('term_starts_on')->nullable()->after('term_label');
            $table->date('term_ends_on')->nullable()->after('term_starts_on');

            $table->index(['term_starts_on', 'term_ends_on']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('board_members', function (Blueprint $table) {
            $table->dropIndex(['term_starts_on', 'term_ends_on']);
            $table->dropColumn(['title', 'term_starts_on', 'term_ends_on']);
        });
    }
};
