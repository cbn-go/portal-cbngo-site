<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Espelho do schema do painel administrativo (cbn-go/portal-cbngo-admin), usado apenas
 * nos ambientes local e testing. Em produção, o portal lê o banco do painel e não migra nada.
 *
 * Origem: database/migrations/2026_10_01_120005_create_news_table.php e 2026_10_24_100000_add_category_to_news_table.php
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('church_id')->nullable()->index()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->index()->constrained()->cascadeOnDelete();
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->string('category', 100)->default('Geral');
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('featured_image', 255)->nullable();
            $table->json('gallery')->nullable();
            $table->date('event_date')->nullable()->index();
            $table->boolean('is_official')->default(false)->index();
            $table->string('status', 20)->default('draft')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index(['category', 'status', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
