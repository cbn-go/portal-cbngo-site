<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Espelho do schema do painel administrativo (cbn-go/portal-cbngo-admin), usado apenas
 * nos ambientes local e testing. Em produção, o portal lê o banco do painel e não migra nada.
 *
 * Origem: database/migrations/2026_10_24_110001_create_documents_table.php
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('category', 40)->default('outros')->index();
            $table->string('file')->nullable();
            $table->string('external_url')->nullable();
            $table->string('file_format', 10)->default('PDF');
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
