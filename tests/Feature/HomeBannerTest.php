<?php

namespace Tests\Feature;

use App\Models\Banner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeBannerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    /**
     * Sem banners vigentes, a home mantém o hero institucional (B.4 / C.8).
     */
    public function test_home_falls_back_to_institutional_hero_without_banners(): void
    {
        Banner::factory()->expired()->create(['title' => 'Banner Vencido']);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Uma Convenção que Cuida, Fortalece e Multiplica');
        $response->assertSee('Conheça a CBN-GO');
        $response->assertDontSee('Banner Vencido');
        $response->assertDontSee('data-testid="hero-banner"', false);
        $this->assertSame(1, substr_count((string) $response->getContent(), '<h1'));
    }

    /**
     * Com um banner, exibe a arte inteira, sem texto sobreposto e sem controles de carrossel (C.1, C.3).
     */
    public function test_home_renders_single_banner_art_without_overlay_or_controls(): void
    {
        Banner::factory()->create([
            'title' => 'Encontro de Missões Nacionais',
            'image' => 'banners/missoes-2026.jpg',
            'alt_text' => 'Encontro de Missões Nacionais 2026, de 12 a 14 de novembro, em Goiânia.',
        ]);

        $response = $this->get('/');
        $html = (string) $response->getContent();

        $response->assertOk();
        $response->assertSee('data-testid="hero-banner"', false);
        $response->assertSee('alt="Encontro de Missões Nacionais 2026, de 12 a 14 de novembro, em Goiânia."', false);
        $response->assertDontSeeText('Encontro de Missões Nacionais');
        $response->assertDontSee('aria-roledescription="carousel"', false);
        $response->assertDontSee('Próximo banner');
        $response->assertDontSee('from-cbn-black/90', false);
        $response->assertDontSee('Uma Convenção que Cuida, Fortalece e Multiplica');

        $image = $this->heroImages($html)[0];
        $this->assertStringContainsString('object-contain', $image);
        $this->assertStringNotContainsString('object-cover', $image);
        $this->assertStringContainsString('width="1920"', $image);
        $this->assertStringContainsString('height="1080"', $image);
        $this->assertStringContainsString('sizes="(min-width: 1600px) 1600px, 100vw"', $image);
        $this->assertStringContainsString('https://midia.test/banners/missoes-2026-cover.webp 1600w', $image);
        $this->assertStringContainsString('fetchpriority="high"', $image);
    }

    /**
     * Sem título visível no hero, a home mantém um único h1 oculto com o nome da organização (C.5).
     */
    public function test_home_keeps_single_visually_hidden_h1_with_banners(): void
    {
        Banner::factory()->count(2)->create();

        $html = (string) $this->get('/')->getContent();

        $this->assertSame(1, substr_count($html, '<h1'));
        $this->assertMatchesRegularExpression('#<h1 class="sr-only">\s*Convenção Batista Nacional do Estado de Goiás\s*</h1>#', $html);
        $this->assertStringNotContainsString('<h2 class="font-brand-serif font-extrabold', $html);
    }

    /**
     * Com vários banners, exibe carrossel acessível na ordem do painel, com controles abaixo da arte (C.6).
     */
    public function test_home_renders_carousel_in_position_order_for_multiple_banners(): void
    {
        Banner::factory()->create(['title' => 'Terceiro aviso', 'alt_text' => 'Arte do terceiro aviso', 'position' => 3]);
        Banner::factory()->create(['title' => 'Primeiro aviso', 'alt_text' => 'Arte do primeiro aviso', 'position' => 1]);
        Banner::factory()->create(['title' => 'Segundo aviso', 'alt_text' => 'Arte do segundo aviso', 'position' => 2]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('aria-roledescription="carousel"', false);
        $response->assertSee('aria-roledescription="slide"', false);
        $response->assertSeeInOrder(['Arte do primeiro aviso', 'Arte do segundo aviso', 'Arte do terceiro aviso']);
        $response->assertSee('Banner anterior');
        $response->assertSee('Próximo banner');
        $response->assertSee('Ir para o banner 3: Terceiro aviso');
        $response->assertSee('aria-label="1 de 3: Primeiro aviso"', false);
        $response->assertSee('Pausar rotação dos banners');
        $response->assertSee('data-testid="hero-banner-controls"', false);

        $images = $this->heroImages((string) $response->getContent());
        $this->assertCount(3, $images);
        $this->assertStringContainsString('fetchpriority="high"', $images[0]);
        $this->assertStringContainsString('loading="eager"', $images[0]);
        foreach (array_slice($images, 1) as $image) {
            $this->assertStringContainsString('loading="lazy"', $image);
            $this->assertStringNotContainsString('fetchpriority', $image);
        }
    }

    /**
     * Banners inativos, agendados ou expirados não aparecem (B.7).
     */
    public function test_home_hides_inactive_scheduled_and_expired_banners(): void
    {
        Banner::factory()->create(['alt_text' => 'Banner Vigente']);
        Banner::factory()->inactive()->create(['alt_text' => 'Banner Desativado']);
        Banner::factory()->scheduled()->create(['alt_text' => 'Banner Agendado']);
        Banner::factory()->expired()->create(['alt_text' => 'Banner Expirado']);

        $response = $this->get('/');

        $response->assertSee('Banner Vigente');
        $response->assertDontSee('Banner Desativado');
        $response->assertDontSee('Banner Agendado');
        $response->assertDontSee('Banner Expirado');
    }

    /**
     * Com link, o banner inteiro é o link: externo em nova aba com rel seguro, interno na mesma aba (C.4).
     */
    public function test_whole_banner_is_the_link_with_target_by_destination(): void
    {
        Banner::factory()->external()->create([
            'position' => 1,
            'link_url' => 'https://www.cbn.org.br/campanha',
            'alt_text' => 'Campanha nacional de missões',
        ]);
        Banner::factory()->create([
            'position' => 2,
            'link_url' => '/igrejas',
            'alt_text' => 'Encontre uma igreja filiada',
        ]);

        $html = (string) $this->get('/')->getContent();

        $this->assertSame(1, preg_match('#<a[^>]*href="https://www\.cbn\.org\.br/campanha"[^>]*>(.*?)</a>#s', $html, $external));
        $this->assertStringContainsString('target="_blank"', $external[0]);
        $this->assertStringContainsString('rel="noopener noreferrer"', $external[0]);
        $this->assertStringContainsString('data-hero-image', $external[1]);
        $this->assertStringContainsString('alt="Campanha nacional de missões"', $external[1]);
        $this->assertStringContainsString('(abre em nova aba)', $external[1]);

        $this->assertSame(1, preg_match('#<a([^>]*)href="/igrejas"([^>]*)>(.*?)</a>#s', $html, $internal));
        $this->assertStringNotContainsString('target=', $internal[1].$internal[2]);
        $this->assertStringContainsString('data-hero-image', $internal[3]);
        $this->assertStringNotContainsString('(abre em nova aba)', $internal[3]);
    }

    /**
     * Sem link válido, a arte não é clicável.
     */
    public function test_banner_without_valid_link_is_not_clickable(): void
    {
        Banner::factory()->create(['position' => 1, 'link_url' => null]);
        Banner::factory()->create(['position' => 2, 'link_url' => 'javascript:alert(1)']);

        $html = (string) $this->get('/')->getContent();

        $this->assertSame(0, preg_match('#<a[^>]*>(?:(?!</a>).)*data-hero-image#s', $html));
        $this->assertStringNotContainsString('javascript:alert(1)', $html);
    }

    /**
     * Editar um banner invalida o cache da home na requisição seguinte (B.2).
     */
    public function test_banner_cache_is_refreshed_when_banner_is_updated(): void
    {
        $banner = Banner::factory()->create(['alt_text' => 'Arte original']);

        $this->get('/')->assertSee('Arte original');

        $this->travel(1)->seconds();
        $banner->update(['alt_text' => 'Arte revisada']);

        $this->get('/')
            ->assertSee('Arte revisada')
            ->assertDontSee('Arte original');
    }

    /**
     * Tags <img> das artes do hero, na ordem do HTML.
     *
     * @return array<int, string>
     */
    private function heroImages(string $html): array
    {
        preg_match_all('#<img[^>]*data-hero-image[^>]*>#', $html, $matches);

        return $matches[0];
    }
}
