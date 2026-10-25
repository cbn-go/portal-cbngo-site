<?php

namespace Tests\Unit;

use App\Models\Banner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BannerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Valida que o scope visible() traz só banners ativos e vigentes, na ordem do painel (B.1).
     */
    public function test_visible_scope_filters_inactive_scheduled_and_expired_and_orders_by_position(): void
    {
        $second = Banner::factory()->create(['title' => 'Segundo', 'position' => 2]);
        $first = Banner::factory()->create([
            'title' => 'Primeiro',
            'position' => 1,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
        ]);
        Banner::factory()->inactive()->create(['position' => 0]);
        Banner::factory()->scheduled()->create(['position' => 0]);
        Banner::factory()->expired()->create(['position' => 0]);

        $this->assertSame([$first->id, $second->id], Banner::visible()->pluck('id')->all());
    }

    /**
     * Valida que o link aceita só http(s) e caminhos relativos locais (proteção XSS).
     */
    public function test_link_accepts_only_http_and_local_relative_paths(): void
    {
        $this->assertSame('/igrejas', (new Banner(['link_url' => ' /igrejas ']))->link);
        $this->assertSame('https://www.cbn.org.br', (new Banner(['link_url' => 'https://www.cbn.org.br']))->link);
        $this->assertNull((new Banner(['link_url' => 'javascript:alert(1)']))->link);
        $this->assertNull((new Banner(['link_url' => '//evil.example.com']))->link);
        $this->assertNull((new Banner(['link_url' => 'data:text/html,oi']))->link);
        $this->assertNull((new Banner(['link_url' => null]))->link);
    }

    /**
     * Valida que só URLs absolutas de outro host são externas e abrem em nova aba.
     */
    public function test_external_links_open_in_new_tab(): void
    {
        config(['app.url' => 'https://cbngo.org.br']);

        $external = new Banner(['link_url' => 'https://www.cbn.org.br/campanha', 'opens_in_new_tab' => false]);
        $sameHost = new Banner(['link_url' => 'https://cbngo.org.br/noticias', 'opens_in_new_tab' => false]);
        $relative = new Banner(['link_url' => '/noticias', 'opens_in_new_tab' => false]);
        $forced = new Banner(['link_url' => '/documentos', 'opens_in_new_tab' => true]);

        $this->assertTrue($external->is_external);
        $this->assertTrue($external->opens_new_tab);
        $this->assertFalse($sameHost->is_external);
        $this->assertFalse($sameHost->opens_new_tab);
        $this->assertFalse($relative->is_external);
        $this->assertFalse($relative->opens_new_tab);
        $this->assertFalse($forced->is_external);
        $this->assertTrue($forced->opens_new_tab);
    }

    /**
     * Valida que a imagem usa o storage de mídia compartilhado e as variantes WebP.
     */
    public function test_image_url_and_srcset_use_shared_media_storage(): void
    {
        config(['media.url' => 'https://midia.cbngo.org.br']);

        $banner = new Banner(['image' => 'banners/assembleia.jpg']);

        $this->assertSame('https://midia.cbngo.org.br/banners/assembleia.jpg', $banner->image_url);
        $this->assertStringContainsString('https://midia.cbngo.org.br/banners/assembleia-cover.webp 1600w', (string) $banner->image_srcset);
    }
}
