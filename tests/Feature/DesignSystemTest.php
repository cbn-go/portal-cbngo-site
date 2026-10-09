<?php

namespace Tests\Feature;

use Tests\TestCase;

class DesignSystemTest extends TestCase
{
    /**
     * Valida que a rota /design-system carrega com sucesso com todos os componentes da vitrine.
     */
    public function test_design_system_showcase_route_renders_successfully(): void
    {
        $this->withoutVite();

        $response = $this->get('/design-system');

        $response->assertStatus(200);
        $response->assertSee('Design System Oficial CBN-GO');
        $response->assertSee('Componentes');
    }

    /**
     * Valida o componente de botão com variantes primária, dourada e polimorfismo de link <a>.
     */
    public function test_button_component_renders_with_variants_and_as_link(): void
    {
        $buttonHtml = $this->blade('<x-button variant="primary">Acessar Portal</x-button>');
        $buttonHtml->assertSee('<button', false);
        $buttonHtml->assertSee('bg-cbn-navy', false);
        $buttonHtml->assertSee('Acessar Portal');

        $linkHtml = $this->blade('<x-button href="https://example.com" variant="gold">Saiba Mais</x-button>');
        $linkHtml->assertSee('<a', false);
        $linkHtml->assertSee('href="https://example.com"', false);
        $linkHtml->assertSee('bg-cbn-gold', false);
        $linkHtml->assertSee('Saiba Mais');
    }

    /**
     * Valida o componente de badge com variantes e marcador (dot).
     */
    public function test_badge_component_renders_with_variants_and_dot(): void
    {
        $badgeHtml = $this->blade('<x-badge variant="gold" :dot="true">Oficial</x-badge>');
        $badgeHtml->assertSee('Oficial');
        $badgeHtml->assertSee('bg-cbn-gold', false);
        $badgeHtml->assertSee('rounded-full', false);
    }

    /**
     * Valida a barra de aviso urgente com mensagem, link e acessibilidade (role="alert").
     */
    public function test_urgent_banner_component_renders_with_message_and_link(): void
    {
        $bannerHtml = $this->blade('<x-urgent-banner message="Convocação para Assembleia Geral" link="/edital" linkText="Ver Edital" />');
        $bannerHtml->assertSee('Convocação para Assembleia Geral');
        $bannerHtml->assertSee('/edital');
        $bannerHtml->assertSee('Ver Edital');
        $bannerHtml->assertSee('role="alert"', false);
    }

    /**
     * Valida o card de notícia com metadados e imagem fallback SVG quando não informada.
     */
    public function test_news_card_component_renders_title_category_and_fallback_image(): void
    {
        $newsHtml = $this->blade('<x-news-card title="Nova Diretoria Eleita" slug="nova-diretoria-eleita" category="Institucional" date="09/10/2026" />');
        $newsHtml->assertSee('Nova Diretoria Eleita');
        $newsHtml->assertSee('Institucional');
        $newsHtml->assertSee('09/10/2026');
        $newsHtml->assertSee('nova-diretoria-eleita');
        $newsHtml->assertSee('<svg', false); // Fallback SVG
    }

    /**
     * Valida o card de artigo com avatar, autor e tempo estimado de leitura.
     */
    public function test_article_card_component_renders_title_author_and_reading_time(): void
    {
        $articleHtml = $this->blade('<x-article-card title="A Fidelidade Pastoral" slug="a-fidelidade-pastoral" authorName="Pr. João Silva" readingTime="5" excerpt="Reflexão teológica sobre liderança." />');
        $articleHtml->assertSee('A Fidelidade Pastoral');
        $articleHtml->assertSee('Pr. João Silva');
        $articleHtml->assertSee('5 min');
        $articleHtml->assertSee('Reflexão teológica sobre liderança.');
    }

    /**
     * Valida que o navbar e footer contêm links institucionais e atributos de acessibilidade.
     */
    public function test_navbar_and_footer_components_render_institutional_links(): void
    {
        $navbarHtml = $this->blade('<x-navbar />');
        $navbarHtml->assertSee('CBN-GO');
        $navbarHtml->assertSee('Quem Somos');
        $navbarHtml->assertSee('Igrejas');
        $navbarHtml->assertSee('aria-expanded="false"', false);

        $footerHtml = $this->blade('<x-footer />');
        $footerHtml->assertSee('Convenção Batista Nacional do Estado de Goiás');
        $footerHtml->assertSee('SETEBAN-GO');
        $footerHtml->assertSee('Goiânia');
    }
}
