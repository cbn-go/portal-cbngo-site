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
     * Sem avisos vigentes, a home mantém o hero institucional de reserva.
     */
    public function test_home_falls_back_to_institutional_hero_without_banners(): void
    {
        Banner::factory()->expired()->create(['title' => 'Aviso Vencido']);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Uma Convenção que Cuida, Fortalece e Multiplica');
        $response->assertSee('Conheça a CBN-GO');
        $response->assertDontSee('Aviso Vencido');
        $response->assertDontSee('data-testid="hero-banner"', false);
        $this->assertSame(1, substr_count((string) $response->getContent(), '<h1'));
    }

    /**
     * O aviso mostra selo, título (h2), subtítulo, botão e data/local ao lado da foto 1:1.
     */
    public function test_banner_renders_square_photo_with_tag_title_subtitle_button_and_meta(): void
    {
        Banner::factory()->create([
            'title' => 'Assembleia Geral Ordinária 2026',
            'tag' => 'Assembleia',
            'subtitle' => 'Inscreva os mensageiros da sua igreja até 28 de fevereiro.',
            'meta' => '14–15 mar · Anápolis',
            'image' => 'banners/assembleia.jpg',
            'alt_text' => 'Pastores reunidos em assembleia',
            'link_url' => '/noticias',
            'link_label' => 'Fazer inscrição',
        ]);

        $response = $this->get('/');
        $html = (string) $response->getContent();

        $response->assertOk();
        $response->assertSee('data-testid="hero-banner"', false);
        $response->assertSee('Avisos da Convenção');
        $this->assertMatchesRegularExpression('#<h2[^>]*>\s*Assembleia Geral Ordinária 2026\s*</h2>#', $html);
        $response->assertSee('data-testid="hero-banner-tag"', false);
        $response->assertSeeInOrder(['Assembleia Geral Ordinária 2026', 'Inscreva os mensageiros da sua igreja até 28 de fevereiro.', 'Fazer inscrição', '14–15 mar · Anápolis']);
        $this->assertMatchesRegularExpression('#<a[^>]*href="/noticias"[^>]*data-hero-cta[^>]*>\s*Fazer inscrição#', $html);
        $response->assertDontSee('Uma Convenção que Cuida, Fortalece e Multiplica');

        $image = $this->heroImages($html)[0];
        $this->assertStringContainsString('alt="Pastores reunidos em assembleia"', $image);
        $this->assertStringContainsString('src="https://midia.test/banners/assembleia-cover.webp"', $image);
        $this->assertStringContainsString('https://midia.test/banners/assembleia-thumb.webp 160w', $image);
        $this->assertStringContainsString('https://midia.test/banners/assembleia-cover.webp 1080w', $image);
        $this->assertStringContainsString('sizes="(min-width: 1024px) 560px, 100vw"', $image);
        $this->assertStringContainsString('width="1080"', $image);
        $this->assertStringContainsString('height="1080"', $image);
        $this->assertStringContainsString('object-cover', $image);
        $this->assertStringContainsString('fetchpriority="high"', $image);
        $this->assertStringContainsString('loading="eager"', $image);
    }

    /**
     * Campos vazios não aparecem e, sem link_label, o botão diz "Saiba mais".
     */
    public function test_empty_fields_are_omitted_and_button_label_defaults(): void
    {
        Banner::factory()->create([
            'title' => 'Aviso enxuto',
            'tag' => null,
            'subtitle' => null,
            'meta' => '  ',
            'link_url' => '/igrejas',
            'link_label' => null,
        ]);

        $html = (string) $this->get('/')->getContent();

        $this->assertStringNotContainsString('data-testid="hero-banner-tag"', $html);
        $this->assertStringNotContainsString('data-hero-subtitle', $html);
        $this->assertStringNotContainsString('data-hero-meta', $html);
        $this->assertMatchesRegularExpression('#data-hero-cta[^>]*>\s*Saiba mais#', $html);
    }

    /**
     * Sem link válido, o aviso não tem botão.
     */
    public function test_banner_without_valid_link_has_no_button(): void
    {
        Banner::factory()->create(['position' => 1, 'link_url' => null]);
        Banner::factory()->create(['position' => 2, 'link_url' => 'javascript:alert(1)']);

        $html = (string) $this->get('/')->getContent();

        $this->assertStringNotContainsString('data-hero-cta', $html);
        $this->assertStringNotContainsString('javascript:alert(1)', $html);
    }

    /**
     * Com um aviso, não há carrossel: sem miniaturas, botões, contador nem Alpine.
     */
    public function test_single_banner_has_no_navigation_or_counter(): void
    {
        Banner::factory()->create(['title' => 'Único aviso']);

        $response = $this->get('/');

        $response->assertSee('Único aviso');
        $response->assertDontSee('aria-roledescription="carousel"', false);
        $response->assertDontSee('data-testid="hero-banner-controls"', false);
        $response->assertDontSee('data-testid="hero-banner-counter"', false);
        $response->assertDontSee('heroCarousel', false);
        $response->assertDontSee('Próximo aviso');
    }

    /**
     * Com vários avisos: carrossel acessível na ordem do painel, com miniaturas, contador e controles.
     */
    public function test_multiple_banners_render_accessible_carousel_in_position_order(): void
    {
        Banner::factory()->create(['title' => 'Terceiro aviso', 'image' => 'banners/c.jpg', 'position' => 3]);
        Banner::factory()->create(['title' => 'Primeiro aviso', 'image' => 'banners/a.jpg', 'position' => 1]);
        Banner::factory()->create(['title' => 'Segundo aviso', 'image' => 'banners/b.jpg', 'position' => 2]);

        $response = $this->get('/');
        $html = (string) $response->getContent();

        $response->assertSee('aria-roledescription="carousel"', false);
        $response->assertSee('aria-label="Avisos em destaque"', false);
        $response->assertSee('aria-roledescription="slide"', false);
        $response->assertSeeInOrder(['Primeiro aviso', 'Segundo aviso', 'Terceiro aviso']);
        $response->assertSee('aria-label="Ir para o aviso 2: Segundo aviso"', false);
        $response->assertSee('Aviso anterior');
        $response->assertSee('Próximo aviso');
        $response->assertSee('Pausar rotação dos avisos');
        $this->assertMatchesRegularExpression('#data-testid="hero-banner-counter"[^>]*>\s*1 / 3\s*<#', $html);

        // Miniaturas 1:1 com a variante thumb, na ordem do painel.
        $response->assertSeeInOrder(['banners/a-thumb.webp', 'banners/b-thumb.webp', 'banners/c-thumb.webp'], false);

        // Só o primeiro slide fica acessível no HTML inicial.
        $this->assertSame(3, substr_count($html, 'aria-roledescription="slide"'));
        $this->assertSame(2, preg_match_all('#aria-roledescription="slide"[^>]*aria-hidden="true" inert#', $html));

        $images = $this->heroImages($html);
        $this->assertCount(3, $images);
        $this->assertStringContainsString('fetchpriority="high"', $images[0]);
        foreach (array_slice($images, 1) as $image) {
            $this->assertStringContainsString('loading="lazy"', $image);
            $this->assertStringNotContainsString('fetchpriority', $image);
        }
    }

    /**
     * A home tem um único h1 (oculto, com o nome da organização); os títulos dos avisos são h2.
     */
    public function test_home_keeps_single_h1_and_banner_titles_are_h2(): void
    {
        Banner::factory()->create(['title' => 'Aviso A', 'position' => 1]);
        Banner::factory()->create(['title' => 'Aviso B', 'position' => 2]);

        $html = (string) $this->get('/')->getContent();

        $this->assertSame(1, substr_count($html, '<h1'));
        $this->assertMatchesRegularExpression('#<h1 class="sr-only">\s*Convenção Batista Nacional do Estado de Goiás\s*</h1>#', $html);
        $this->assertMatchesRegularExpression('#<h2[^>]*>\s*Aviso A\s*</h2>#', $html);
        $this->assertMatchesRegularExpression('#<h2[^>]*>\s*Aviso B\s*</h2>#', $html);
    }

    /**
     * Banners legados (16:9, sem foto 1:1), inativos, agendados ou expirados não aparecem.
     */
    public function test_home_hides_legacy_inactive_scheduled_and_expired_banners(): void
    {
        Banner::factory()->create(['title' => 'Aviso Vigente']);
        Banner::factory()->legacy()->create(['title' => 'Banner 16:9 Antigo']);
        Banner::factory()->inactive()->create(['title' => 'Aviso Desativado']);
        Banner::factory()->scheduled()->create(['title' => 'Aviso Agendado']);
        Banner::factory()->expired()->create(['title' => 'Aviso Expirado']);

        $response = $this->get('/');

        $response->assertSee('Aviso Vigente');
        $response->assertDontSee('Banner 16:9 Antigo');
        $response->assertDontSee('Aviso Desativado');
        $response->assertDontSee('Aviso Agendado');
        $response->assertDontSee('Aviso Expirado');
    }

    /**
     * O botão abre link externo em nova aba com rel seguro; link interno abre na mesma aba.
     */
    public function test_button_target_follows_destination(): void
    {
        Banner::factory()->external()->create(['position' => 1, 'link_url' => 'https://www.cbn.org.br/campanha']);
        Banner::factory()->create(['position' => 2, 'link_url' => '/igrejas']);

        $html = (string) $this->get('/')->getContent();

        $this->assertSame(1, preg_match('#<a[^>]*href="https://www\.cbn\.org\.br/campanha"[^>]*>(.*?)</a>#s', $html, $external));
        $this->assertStringContainsString('target="_blank"', $external[0]);
        $this->assertStringContainsString('rel="noopener noreferrer"', $external[0]);
        $this->assertStringContainsString('(abre em nova aba)', $external[1]);

        $this->assertSame(1, preg_match('#<a([^>]*)href="/igrejas"([^>]*data-hero-cta[^>]*)>(.*?)</a>#s', $html, $internal));
        $this->assertStringNotContainsString('target=', $internal[1].$internal[2]);
        $this->assertStringNotContainsString('(abre em nova aba)', $internal[3]);
    }

    /**
     * Editar um aviso invalida o cache da home na requisição seguinte.
     */
    public function test_banner_cache_is_refreshed_when_banner_is_updated(): void
    {
        $banner = Banner::factory()->create(['title' => 'Título original']);

        $this->get('/')->assertSee('Título original');

        $this->travel(1)->seconds();
        $banner->update(['title' => 'Título revisado']);

        $this->get('/')
            ->assertSee('Título revisado')
            ->assertDontSee('Título original');
    }

    /**
     * Só a hero muda: as seções seguintes da home são idênticas com e sem avisos.
     */
    public function test_sections_after_hero_are_unchanged(): void
    {
        $withoutBanners = $this->sectionsAfterHero((string) $this->get('/')->getContent());

        Banner::factory()->count(2)->create();
        $withBanners = $this->sectionsAfterHero((string) $this->get('/')->getContent());

        $this->assertNotSame('', $withoutBanners);
        $this->assertSame($withoutBanners, $withBanners);
    }

    /**
     * HTML da home a partir da primeira seção depois da hero (notícias) até o fim do <main>.
     */
    private function sectionsAfterHero(string $html): string
    {
        preg_match('#<section[^>]*id="noticias".*</main>#s', $html, $matches);

        return $matches[0] ?? '';
    }

    /**
     * Tags <img> das fotos dos avisos, na ordem do HTML.
     *
     * @return array<int, string>
     */
    private function heroImages(string $html): array
    {
        preg_match_all('#<img[^>]*data-hero-image[^>]*>#', $html, $matches);

        return $matches[0];
    }
}
