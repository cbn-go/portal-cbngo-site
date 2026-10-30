<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Espelho do schema do painel administrativo (cbn-go/portal-cbngo-admin), usado apenas
 * nos ambientes local e testing. Em produção, o portal lê o banco do painel e não migra nada.
 *
 * Origem: database/migrations/2026_10_30_120000_create_statutes_table.php
 *       + database/migrations/2026_10_30_130000_add_audit_fields_to_statutes_table.php
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('statutes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('version_label', 50)->unique();
            $table->date('approved_at');
            $table->string('approved_place', 150);
            $table->string('approval_body', 150);
            $table->string('minutes_reference')->nullable();
            $table->text('registry_info')->nullable();
            $table->date('effective_at');
            $table->string('status', 30)->default('draft')->index();
            $table->longText('akn_xml');
            $table->string('original_pdf')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'effective_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statutes');
    }
};
