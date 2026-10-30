<?php

namespace Tests\Feature;

use App\Enums\ProjectCategory;
use App\Models\Church;
use App\Models\SocialProject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialProjectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    /**
     * A listagem mostra só projetos publicados; rascunho, pendente, arquivado e agendado nunca aparecem (P.2, P.8).
     */
    public function test_index_lists_only_published_projects(): void
    {
        SocialProject::factory()->create(['title' => 'Projeto Publicado']);
        SocialProject::factory()->draft()->create(['title' => 'Projeto Rascunho']);
        SocialProject::factory()->pendingReview()->create(['title' => 'Projeto Pendente']);
        SocialProject::factory()->archived()->create(['title' => 'Projeto Arquivado']);
        SocialProject::factory()->create(['title' => 'Projeto Agendado', 'published_at' => now()->addDay()]);

        $response = $this->get('/projetos-sociais');

        $response->assertOk();
        $response->assertSee('Projetos Sociais');
        $response->assertSee('Pacto de Lausanne');
        $response->assertSee(config('cbngo.lausanne_url'), false);
        $response->assertSee('Projeto Publicado');
        $response->assertDontSee('Projeto Rascunho');
        $response->assertDontSee('Projeto Pendente');
        $response->assertDontSee('Projeto Arquivado');
        $response->assertDontSee('Projeto Agendado');
    }

    /**
     * Destaques aparecem primeiro, depois os publicados mais recentemente (P.2).
     */
    public function test_index_orders_featured_first_then_by_published_at(): void
    {
        SocialProject::factory()->create(['title' => 'Recente comum', 'published_at' => now()->subHour()]);
        SocialProject::factory()->featured()->create(['title' => 'Antigo em destaque', 'published_at' => now()->subMonth()]);
        SocialProject::factory()->create(['title' => 'Antigo comum', 'published_at' => now()->subWeeks(2)]);

        $this->get('/projetos-sociais')->assertSeeInOrder(['Antigo em destaque', 'Recente comum', 'Antigo comum']);
    }

    /**
     * Cards exibem capa, badge da categoria, igreja, cidade, resumo e selo "Em andamento" (P.2, P.7).
     */
    public function test_index_cards_show_category_church_city_summary_and_ongoing_badge(): void
    {
        $church = Church::factory()->create(['name' => 'Igreja Batista Nacional Esperança', 'city' => 'Anápolis']);

        SocialProject::factory()->forChurch($church)->category(ProjectCategory::EDUCACAO)->create([
            'title' => 'Reforço Escolar Esperança',
            'summary' => 'Aulas de reforço para crianças do bairro.',
            'cover_image' => 'projects/covers/reforco.jpg',
        ]);
        SocialProject::factory()->ended()->create(['title' => 'Projeto Encerrado']);

        $response = $this->get('/projetos-sociais');

        $response->assertSee('Reforço Escolar Esperança');
        $response->assertSee('Educação');
        $response->assertSee('bg-sky-800', false);
        $response->assertSee('Igreja Batista Nacional Esperança');
        $response->assertSee('Anápolis');
        $response->assertSee('Aulas de reforço para crianças do bairro.');
        $response->assertSee('https://midia.test/projects/covers/reforco-card.webp 800w', false);
        $response->assertSee('loading="lazy"', false);
        $this->assertSame(1, substr_count((string) $response->getContent(), 'Em andamento'));
    }

    /**
     * Paginação de 9 projetos por página, preservando filtros.
     */
    public function test_index_paginates_nine_projects_per_page(): void
    {
        SocialProject::factory()->count(12)->category(ProjectCategory::SOCIAL)->create();

        $response = $this->get('/projetos-sociais?categoria=social');

        $this->assertSame(9, substr_count((string) $response->getContent(), 'data-testid="project-card"'));
        $response->assertSee('categoria=social&amp;page=2', false);

        $page2 = $this->get('/projetos-sociais?categoria=social&page=2');
        $this->assertSame(3, substr_count((string) $page2->getContent(), 'data-testid="project-card"'));
    }

    /**
     * O filtro por categoria usa o slug sem acento; slug inválido é ignorado (P.2, P.8).
     */
    public function test_index_filters_by_category_slug_and_ignores_invalid_slug(): void
    {
        SocialProject::factory()->category(ProjectCategory::LIDERANCA)->create(['title' => 'Escola de Líderes']);
        SocialProject::factory()->category(ProjectCategory::CULTURAL)->create(['title' => 'Coral Comunitário']);

        $filtered = $this->get('/projetos-sociais?categoria=lideranca');
        $filtered->assertSee('Escola de Líderes');
        $filtered->assertDontSee('Coral Comunitário');
        $filtered->assertSee('Pacto de Lausanne, art. 11: Educação e liderança');
        $filtered->assertSee('aria-current="page"', false);

        $invalid = $this->get('/projetos-sociais?categoria=inexistente');
        $invalid->assertSee('Escola de Líderes');
        $invalid->assertSee('Coral Comunitário');
    }

    /**
     * Os links de categoria preservam busca, cidade e igreja, para a URL poder ser compartilhada.
     */
    public function test_category_links_preserve_other_filters(): void
    {
        SocialProject::factory()->create(['city' => 'Goiânia']);

        $response = $this->get('/projetos-sociais?cidade=Goi%C3%A2nia&q=horta');

        $response->assertSee(e(route('projetos-sociais.index', ['q' => 'horta', 'cidade' => 'Goiânia', 'categoria' => 'cultural'])), false);
        $response->assertSee(e(route('projetos-sociais.index', ['q' => 'horta', 'cidade' => 'Goiânia'])), false);
    }

    /**
     * Filtros por cidade, por igreja e busca por texto (P.2, P.8).
     */
    public function test_index_filters_by_city_church_and_search(): void
    {
        $anapolis = Church::factory()->create(['city' => 'Anápolis', 'slug' => 'ibn-anapolis']);
        $rioVerde = Church::factory()->create(['city' => 'Rio Verde', 'slug' => 'ibn-rio-verde']);

        SocialProject::factory()->forChurch($anapolis)->create(['title' => 'Sopão Solidário', 'summary' => 'Refeições às quintas.']);
        SocialProject::factory()->forChurch($rioVerde)->create(['title' => 'Horta Comunitária', 'summary' => 'Cultivo de alimentos.']);

        $byCity = $this->get('/projetos-sociais?cidade=An%C3%A1polis');
        $byCity->assertSee('Sopão Solidário');
        $byCity->assertDontSee('Horta Comunitária');
        $byCity->assertSee('<option value="Rio Verde"', false);

        $byChurch = $this->get('/projetos-sociais?igreja=ibn-rio-verde');
        $byChurch->assertSee('Horta Comunitária');
        $byChurch->assertDontSee('Sopão Solidário');
        $byChurch->assertSee('<option value="ibn-anapolis"', false);

        $bySearch = $this->get('/projetos-sociais?q=alimentos');
        $bySearch->assertSee('Horta Comunitária');
        $bySearch->assertDontSee('Sopão Solidário');
    }

    /**
     * Estado vazio quando nenhum projeto corresponde aos filtros (P.2).
     */
    public function test_index_shows_empty_state_when_no_project_matches(): void
    {
        SocialProject::factory()->category(ProjectCategory::SOCIAL)->create();

        $response = $this->get('/projetos-sociais?categoria=cultural');

        $response->assertSee('Nenhum projeto encontrado');
        $response->assertSee('Limpar filtros');
    }

    /**
     * O detalhe exibe ficha, link da igreja no diretório, descrição sanitizada e galeria (P.3).
     */
    public function test_show_renders_project_details_church_link_sanitized_description_and_gallery(): void
    {
        $church = Church::factory()->create(['name' => 'Igreja Batista Nacional Vida', 'slug' => 'ibn-vida', 'city' => 'Jataí']);

        SocialProject::factory()->forChurch($church)->category(ProjectCategory::SOCIAL)->create([
            'title' => 'Cesta Básica Vida',
            'slug' => 'cesta-basica-vida',
            'summary' => 'Distribuição mensal de cestas básicas.',
            'description' => '<p>Atendemos famílias do bairro.</p><img src="x" onerror="alert(1)"><a href="javascript:alert(1)">clique</a>',
            'cover_image' => 'projects/covers/cesta.jpg',
            'gallery' => ['projects/galleries/foto1.jpg', 'projects/galleries/foto2.jpg'],
            'target_audience' => 'Famílias em vulnerabilidade',
            'beneficiaries_count' => 1250,
            'started_at' => '2026-03-01',
            'ended_at' => null,
        ]);

        $response = $this->get('/projetos-sociais/cesta-basica-vida');

        $response->assertOk();
        $response->assertSee('Cesta Básica Vida');
        $response->assertSee('Distribuição mensal de cestas básicas.');
        $response->assertSee('Igreja Batista Nacional Vida');
        $response->assertSee('href="'.route('igrejas.show', 'ibn-vida').'"', false);
        $response->assertSee('Jataí');
        $response->assertSee('Desde mar/2026');
        $response->assertSee('Famílias em vulnerabilidade');
        $response->assertSee('1.250 pessoas');
        $response->assertSee('Pacto de Lausanne, art. 5: Responsabilidade social cristã');
        $response->assertSee('Atendemos famílias do bairro.');
        $response->assertDontSee('onerror', false);
        $response->assertDontSee('javascript:alert(1)', false);
        $response->assertSee('Galeria de Fotos');
        $response->assertSee('https://midia.test/projects/galleries/foto2.jpg', false);
        $response->assertSee('https://midia.test/projects/covers/cesta.jpg', false);
        $response->assertSee('Como ajudar');
        $response->assertSee('Copiar link do projeto');
    }

    /**
     * Com telefone válido, "Como ajudar" exibe botão de WhatsApp com mensagem; sem telefone, não (P.3).
     */
    public function test_show_renders_whatsapp_button_only_with_valid_phone(): void
    {
        SocialProject::factory()->create([
            'slug' => 'com-telefone',
            'title' => 'Projeto Com Telefone',
            'contact_name' => 'Irmã Ana',
            'contact_phone' => '(62) 99876-5432',
            'contact_email' => 'ana@ibnvida.org.br',
            'how_to_help' => 'Doe roupas de inverno.',
        ]);
        SocialProject::factory()->create(['slug' => 'sem-telefone', 'contact_phone' => null, 'contact_email' => 'invalido']);

        $with = $this->get('/projetos-sociais/com-telefone');
        $with->assertSee('https://wa.me/5562998765432?text=', false);
        $with->assertSee('Falar no WhatsApp');
        $with->assertSee('Irmã Ana');
        $with->assertSee('Doe roupas de inverno.');
        $with->assertSee('mailto:ana@ibnvida.org.br', false);

        $without = $this->get('/projetos-sociais/sem-telefone');
        $without->assertDontSee('Falar no WhatsApp');
        $without->assertDontSee(rawurlencode('Quero ajudar no projeto'), false);
        $without->assertDontSee('mailto:invalido', false);
    }

    /**
     * "Outros projetos" lista até 3 projetos publicados da mesma categoria, sem o atual (P.3).
     */
    public function test_show_lists_other_projects_of_same_category(): void
    {
        SocialProject::factory()->category(ProjectCategory::CULTURAL)->create(['slug' => 'atual', 'title' => 'Projeto Atual']);
        SocialProject::factory()->count(4)->category(ProjectCategory::CULTURAL)->create();
        SocialProject::factory()->category(ProjectCategory::CULTURAL)->draft()->create(['title' => 'Cultural Rascunho']);
        SocialProject::factory()->category(ProjectCategory::SOCIAL)->create(['title' => 'Projeto de Outra Categoria']);

        $response = $this->get('/projetos-sociais/atual');

        $response->assertSee('Outros projetos de Cultural');
        $this->assertSame(3, substr_count((string) $response->getContent(), 'data-testid="project-card"'));
        $response->assertDontSee('Cultural Rascunho');
        $response->assertDontSee('Projeto de Outra Categoria');
    }

    /**
     * Slug inexistente ou de projeto não publicado retorna 404 (P.8).
     */
    public function test_show_returns_404_for_missing_or_unpublished_project(): void
    {
        SocialProject::factory()->draft()->create(['slug' => 'rascunho']);
        SocialProject::factory()->pendingReview()->create(['slug' => 'pendente']);
        SocialProject::factory()->create(['slug' => 'agendado', 'published_at' => now()->addDay()]);

        $this->get('/projetos-sociais/nao-existe')->assertNotFound();
        $this->get('/projetos-sociais/rascunho')->assertNotFound();
        $this->get('/projetos-sociais/pendente')->assertNotFound();
        $this->get('/projetos-sociais/agendado')->assertNotFound();
    }

    /**
     * Projeto de igreja desativada continua visível, sem igreja nem link do diretório.
     */
    public function test_project_of_inactive_church_hides_church_link(): void
    {
        $church = Church::factory()->create(['name' => 'Igreja Desativada', 'is_active' => false]);
        SocialProject::factory()->forChurch($church)->create(['slug' => 'orfao', 'title' => 'Projeto Órfão']);

        $response = $this->get('/projetos-sociais/orfao');

        $response->assertOk();
        $response->assertSee('Projeto Órfão');
        $response->assertDontSee('Igreja Desativada');
    }

    /**
     * Menu principal e rodapé têm o item "Projetos Sociais", marcado como ativo na seção (P.4).
     */
    public function test_navigation_and_footer_link_to_social_projects(): void
    {
        $html = (string) $this->get('/projetos-sociais')->getContent();

        $this->assertMatchesRegularExpression('#<a\s+href="'.preg_quote(route('projetos-sociais.index'), '#').'"[^>]*aria-current="page"#', $html);
        $this->assertGreaterThanOrEqual(3, substr_count($html, 'href="'.route('projetos-sociais.index').'"'));
    }
}
