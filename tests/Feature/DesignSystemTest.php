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
     * Valida o card de notícia com metadados, fallback SVG e anéis de foco visível (WCAG 2.4.7 AA).
     */
    public function test_news_card_component_renders_title_category_and_fallback_image(): void
    {
        $newsHtml = $this->blade('<x-news-card title="Nova Diretoria Eleita" slug="nova-diretoria-eleita" category="Institucional" date="09/10/2026" />');
        $newsHtml->assertSee('Nova Diretoria Eleita');
        $newsHtml->assertSee('Institucional');
        $newsHtml->assertSee('09/10/2026');
        $newsHtml->assertSee('nova-diretoria-eleita');
        $newsHtml->assertSee('<svg', false); // Fallback SVG
        $newsHtml->assertSee('focus-visible:ring-cbn-gold', false); // A11y focus ring
    }

    /**
     * Valida o card de artigo com avatar, autor, tempo de leitura e anel de foco visível.
     */
    public function test_article_card_component_renders_title_author_and_reading_time(): void
    {
        $articleHtml = $this->blade('<x-article-card title="A Fidelidade Pastoral" slug="a-fidelidade-pastoral" authorName="Pr. João Silva" readingTime="5" excerpt="Reflexão teológica sobre liderança." />');
        $articleHtml->assertSee('A Fidelidade Pastoral');
        $articleHtml->assertSee('Pr. João Silva');
        $articleHtml->assertSee('5 min');
        $articleHtml->assertSee('Reflexão teológica sobre liderança.');
        $articleHtml->assertSee('focus-visible:ring-cbn-gold', false); // A11y focus ring
    }

    /**
     * Valida que o navbar e footer contêm links institucionais, redes sociais e acessibilidade.
     */
    public function test_navbar_and_footer_components_render_institutional_links(): void
    {
        $navbarHtml = $this->blade('<x-navbar />');
        $navbarHtml->assertSee('images/brand/cbn-go-on-dark.png', false);
        $navbarHtml->assertSee('CBN | GO', false);
        $navbarHtml->assertSee('Quem Somos');
        $navbarHtml->assertSee('Igrejas');
        $navbarHtml->assertSee('aria-expanded', false);

        $footerHtml = $this->blade('<x-footer />');
        $footerHtml->assertSee('images/brand/cbn-go-on-dark.png', false);
        $footerHtml->assertSee('Convenção Batista Nacional do Estado de Goiás');
        $footerHtml->assertSee('SETEBAN-GO');
        $footerHtml->assertSee('Goiânia');
        // Redes sociais exigidas no AC 2.2:
        $footerHtml->assertSee('Instagram');
        $footerHtml->assertSee('Facebook');
        $footerHtml->assertSee('YouTube');
        $footerHtml->assertSee('WhatsApp');
    }

    /**
     * Valida que a prop active do navbar destaca o item correto tanto no desktop quanto no mobile.
     */
    public function test_navbar_highlights_specified_active_item_on_desktop_and_mobile(): void
    {
        $navbarHtml = $this->blade('<x-navbar active="noticias" />');

        // Notícias deve ter classes ativas
        $navbarHtml->assertSee('text-cbn-gold font-semibold bg-white/5', false);
        $navbarHtml->assertSee('Notícias');
    }

    /**
     * Garante que o drawer mobile não usa Tailwind `hidden` junto com Alpine x-show
     * (conflito que impede o menu de abrir quando Alpine está carregado).
     */
    public function test_navbar_mobile_drawer_uses_alpine_visibility_without_tailwind_hidden(): void
    {
        $navbarHtml = $this->blade('<x-navbar />');

        $navbarHtml->assertSee('id="cbn-mobile-menu"', false);
        $navbarHtml->assertSee('x-show="open"', false);
        $navbarHtml->assertSee('x-cloak', false);
        $navbarHtml->assertSee('lg:hidden', false);
        $navbarHtml->assertDontSee('hidden lg:hidden', false);
    }
}
