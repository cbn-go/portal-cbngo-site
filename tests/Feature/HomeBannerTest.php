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
     * Sem banners vigentes, a home mantém o hero institucional (B.4).
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
    }

    /**
     * Com um banner, exibe hero simples sem controles de carrossel (B.3).
     */
    public function test_home_renders_single_banner_without_carousel_controls(): void
    {
        Banner::factory()->create([
            'title' => 'Matrículas abertas no SETEBAN-GO',
            'subtitle' => 'Bacharelado em Teologia com aulas em Goiânia.',
            'image' => 'banners/seteban.jpg',
            'alt_text' => 'Alunos em sala de aula do seminário',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('data-testid="hero-banner"', false);
        $response->assertSee('Matrículas abertas no SETEBAN-GO');
        $response->assertSee('Bacharelado em Teologia com aulas em Goiânia.');
        $response->assertSee('alt="Alunos em sala de aula do seminário"', false);
        $response->assertSee('https://midia.test/banners/seteban-cover.webp 1600w', false);
        $response->assertSee('fetchpriority="high"', false);
        $response->assertDontSee('aria-roledescription="carousel"', false);
        $response->assertDontSee('Próximo banner');
        $response->assertDontSee('Uma Convenção que Cuida, Fortalece e Multiplica');
    }

    /**
     * Com vários banners, exibe carrossel acessível na ordem do painel (B.3, B.5, B.6).
     */
    public function test_home_renders_carousel_in_position_order_for_multiple_banners(): void
    {
        Banner::factory()->create(['title' => 'Terceiro aviso', 'position' => 3]);
        Banner::factory()->create(['title' => 'Primeiro aviso', 'position' => 1]);
        Banner::factory()->create(['title' => 'Segundo aviso', 'position' => 2]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('aria-roledescription="carousel"', false);
        $response->assertSee('aria-roledescription="slide"', false);
        $response->assertSeeInOrder(['Primeiro aviso', 'Segundo aviso', 'Terceiro aviso']);
        $response->assertSee('Banner anterior');
        $response->assertSee('Próximo banner');
        $response->assertSee('Ir para o banner 3');
        $response->assertSee('Pausar rotação dos banners');

        preg_match_all('#<img[^>]*data-hero-image[^>]*>#', (string) $response->getContent(), $images);
        $this->assertCount(3, $images[0]);
        $this->assertStringContainsString('fetchpriority="high"', $images[0][0]);
        $this->assertStringContainsString('loading="eager"', $images[0][0]);
        foreach (array_slice($images[0], 1) as $image) {
            $this->assertStringContainsString('loading="lazy"', $image);
            $this->assertStringNotContainsString('fetchpriority', $image);
        }
        $this->assertSame(1, substr_count((string) $response->getContent(), '<h1'));
    }

    /**
     * Banners inativos, agendados ou expirados não aparecem (B.7).
     */
    public function test_home_hides_inactive_scheduled_and_expired_banners(): void
    {
        Banner::factory()->create(['title' => 'Banner Vigente']);
        Banner::factory()->inactive()->create(['title' => 'Banner Desativado']);
        Banner::factory()->scheduled()->create(['title' => 'Banner Agendado']);
        Banner::factory()->expired()->create(['title' => 'Banner Expirado']);

        $response = $this->get('/');

        $response->assertSee('Banner Vigente');
        $response->assertDontSee('Banner Desativado');
        $response->assertDontSee('Banner Agendado');
        $response->assertDontSee('Banner Expirado');
    }

    /**
     * Link externo abre em nova aba com rel seguro; link interno abre na mesma aba (B.3).
     */
    public function test_banner_links_render_target_and_rel_by_destination(): void
    {
        Banner::factory()->external()->create([
            'position' => 1,
            'link_url' => 'https://www.cbn.org.br/campanha',
            'link_label' => 'Ver campanha nacional',
        ]);
        Banner::factory()->create([
            'position' => 2,
            'link_url' => '/igrejas',
            'link_label' => 'Encontrar igreja filiada',
        ]);

        $response = $this->get('/');
        $html = (string) $response->getContent();

        $this->assertSame(1, preg_match('#<a[^>]*href="https://www\.cbn\.org\.br/campanha"[^>]*>#', $html, $external));
        $this->assertStringContainsString('target="_blank"', $external[0]);
        $this->assertStringContainsString('rel="noopener noreferrer"', $external[0]);

        $this->assertSame(1, preg_match('#<a[^>]*href="/igrejas"[^>]*>\s*Encontrar igreja filiada#', $html, $internal));
        $this->assertStringNotContainsString('target=', $internal[0]);
        $response->assertSee('Ver campanha nacional');
        $response->assertSee('(abre em nova aba)');
        $response->assertSee('Encontrar igreja filiada');
    }

    /**
     * Sem link válido, o banner não exibe botão.
     */
    public function test_banner_without_valid_link_has_no_button(): void
    {
        Banner::factory()->create(['position' => 1, 'link_url' => null, 'link_label' => 'Botão sem destino']);
        Banner::factory()->create(['position' => 2, 'link_url' => 'javascript:alert(1)', 'link_label' => 'Botão malicioso']);

        $response = $this->get('/');

        $response->assertDontSee('Botão sem destino');
        $response->assertDontSee('Botão malicioso');
        $response->assertDontSee('javascript:alert(1)', false);
    }

    /**
     * O ponto focal do painel controla o corte da imagem (object-position).
     */
    public function test_banner_focal_point_is_applied_as_object_position(): void
    {
        Banner::factory()->create(['focal_point' => 'left']);

        $this->get('/')->assertSee('object-position: left center', false);
    }

    /**
     * Editar um banner invalida o cache da home na requisição seguinte (B.2).
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
}
