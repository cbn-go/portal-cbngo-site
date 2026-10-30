<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Author;
use App\Models\Church;
use App\Models\News;
use App\Models\SocialProject;
use App\Models\UrgentNotice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Valida que o endpoint nativo /up do Laravel responde com HTTP 200.
     */
    public function test_native_up_endpoint_returns_ok_status(): void
    {
        $response = $this->get('/up');

        $response->assertStatus(200);
    }

    /**
     * Valida que o endpoint de healthcheck /health retorna HTTP 200 com JSON esperado.
     */
    public function test_healthcheck_endpoint_returns_ok_status(): void
    {
        $response = $this->get('/health');

        $response->assertStatus(200);
        $response->assertExactJson([
            'status' => 'ok',
        ]);
    }

    /**
     * Valida que a Hero Section renderiza o nome oficial da CBN Goiás,
     * o lema da convenção e os botões de ação institucional (AC 3.2).
     */
    public function test_home_renders_hero_section_with_institutional_motto_and_actions(): void
    {
        $this->withoutVite();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Convenção Batista Nacional do Estado de Goiás');
        $response->assertSee('Uma Convenção que Cuida, Fortalece e Multiplica');
        $response->assertSee('Conheça a CBN-GO');
        $response->assertSee('/quem-somos');
        $response->assertSee('Encontre uma Igreja');
        $response->assertSee('/igrejas');
    }

    /**
     * Valida que a barra de avisos urgentes é exibida no topo quando houver aviso ativo e vigente (AC 3.1).
     */
    public function test_home_renders_active_urgent_banner_when_available(): void
    {
        $this->withoutVite();

        UrgentNotice::factory()->create([
            'title' => 'Comunicado de Assembleia Extraordinária',
            'content' => 'Convocação oficial para a Assembleia Geral da CBN-GO em Anápolis.',
            'action_url' => 'https://cbngo.org.br/edital-2026',
            'action_label' => 'Ler Edital',
            'priority' => 'urgent',
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDays(2),
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Convocação oficial para a Assembleia Geral da CBN-GO em Anápolis.');
        $response->assertSee('Ler Edital');
        $response->assertSee('https://cbngo.org.br/edital-2026');
        $response->assertSee('AVISO OFICIAL:');
    }

    /**
     * Valida que avisos inativos, expirados ou com vigência futura não são exibidos na Home (AC 3.1).
     */
    public function test_home_does_not_render_urgent_banner_when_inactive_or_expired(): void
    {
        $this->withoutVite();

        // Aviso inativo
        UrgentNotice::factory()->create([
            'title' => 'Aviso Inativo',
            'content' => 'Este aviso não deve aparecer porque está inativo.',
            'is_active' => false,
        ]);

        // Aviso expirado
        UrgentNotice::factory()->create([
            'title' => 'Aviso Expirado',
            'content' => 'Este aviso não deve aparecer porque já expirou.',
            'is_active' => true,
            'expires_at' => now()->subMinute(),
        ]);

        // Aviso futuro
        UrgentNotice::factory()->create([
            'title' => 'Aviso Futuro',
            'content' => 'Este aviso não deve aparecer porque ainda não entrou em vigência.',
            'is_active' => true,
            'starts_at' => now()->addHour(),
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Este aviso não deve aparecer porque está inativo.');
        $response->assertDontSee('Este aviso não deve aparecer porque já expirou.');
        $response->assertDontSee('Este aviso não deve aparecer porque ainda não entrou em vigência.');
        $response->assertDontSee('AVISO OFICIAL:');
    }

    /**
     * Valida que a seção de notícias exibe as 4 mais recentes publicadas: manchete + 3 chamadas (AC 3.3).
     */
    public function test_home_renders_latest_published_news(): void
    {
        $this->withoutVite();

        News::factory()->create([
            'title' => 'Notícia Antiga Fora do Top 4',
            'slug' => 'noticia-antiga-fora-do-top-4',
            'category' => 'Geral',
            'excerpt' => 'Resumo da notícia antiga.',
            'content' => 'Conteúdo detalhado da notícia antiga.',
            'status' => 'published',
            'published_at' => now()->subDays(10),
        ]);

        News::factory()->create([
            'title' => 'Mutirão Missionário no Entorno do DF',
            'slug' => 'mutirao-missionario-entorno-df',
            'category' => 'Missões',
            'status' => 'published',
            'published_at' => now()->subDays(5),
        ]);

        News::factory()->create([
            'title' => 'Retiro Estadual de Pastores 2026',
            'slug' => 'retiro-estadual-de-pastores-2026',
            'category' => 'Eventos',
            'excerpt' => 'Encontro de comunhão e edificação pastoral em Pirenópolis.',
            'content' => 'Conteúdo detalhado do retiro.',
            'status' => 'published',
            'published_at' => now()->subDays(3),
        ]);

        News::factory()->create([
            'title' => 'Nova Congregação Inaugurada em Rio Verde',
            'slug' => 'nova-congregacao-inaugurada-em-rio-verde',
            'category' => 'Missões',
            'excerpt' => 'Avanço missionário da CBN Goiás no sudoeste do estado.',
            'content' => 'Conteúdo detalhado da inauguração.',
            'status' => 'published',
            'published_at' => now()->subDays(2),
        ]);

        News::factory()->create([
            'title' => 'Comunicado Oficial da Diretoria da CBN-GO',
            'slug' => 'comunicado-oficial-da-diretoria-cbn-go',
            'category' => 'Comunicado Oficial',
            'excerpt' => 'Orientações pastorais sobre o ano eclesiástico de 2026.',
            'content' => 'Conteúdo completo do comunicado.',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Últimas Notícias e Comunicados');
        $response->assertSee('Comunicado Oficial da Diretoria da CBN-GO');
        $response->assertSee('Nova Congregação Inaugurada em Rio Verde');
        $response->assertSee('Retiro Estadual de Pastores 2026');
        $response->assertSee('/noticias/comunicado-oficial-da-diretoria-cbn-go');
        $response->assertSee('Mutirão Missionário no Entorno do DF');
        $response->assertDontSee('Notícia Antiga Fora do Top 4');
    }

    /**
     * Valida que notícias em rascunho ou com agendamento futuro são ocultadas da Home (AC 3.3).
     */
    public function test_home_hides_draft_or_future_news(): void
    {
        $this->withoutVite();

        News::factory()->create([
            'title' => 'Notícia Não Publicada em Rascunho',
            'slug' => 'noticia-rascunho',
            'category' => 'Geral',
            'excerpt' => 'Resumo rascunho.',
            'content' => 'Conteúdo rascunho.',
            'status' => 'draft',
            'published_at' => now()->subDay(),
        ]);

        News::factory()->create([
            'title' => 'Notícia com Publicação Agendada Futura',
            'slug' => 'noticia-futura',
            'category' => 'Geral',
            'excerpt' => 'Resumo futuro.',
            'content' => 'Conteúdo futuro.',
            'status' => 'published',
            'published_at' => now()->addDays(2),
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Notícia Não Publicada em Rascunho');
        $response->assertDontSee('Notícia com Publicação Agendada Futura');
    }

    /**
     * Valida que a seção de artigos exibe os 3 mais recentes com os dados do autor (AC 3.4).
     */
    public function test_home_renders_latest_published_articles_with_author(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor([
            'name' => 'Pr. Roberto Silveira',
            'slug' => 'pr-roberto-silveira',
            'pastoral_title' => 'Pastor e Teólogo',
        ]);

        Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Artigo Antigo Fora do Top 4',
            'slug' => 'artigo-antigo-fora-top-4',
            'excerpt' => 'Resumo artigo antigo.',
            'content' => 'Conteúdo artigo antigo.',
            'reading_time' => 4,
            'status' => 'published',
            'published_at' => now()->subDays(15),
        ]);

        Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Discipulado nas Pequenas Comunidades',
            'slug' => 'discipulado-pequenas-comunidades',
            'excerpt' => 'Resumo sobre discipulado.',
            'content' => 'Conteúdo sobre discipulado.',
            'reading_time' => 6,
            'status' => 'published',
            'published_at' => now()->subDays(8),
        ]);

        Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'A Centralidade de Cristo na Vida Comunitária',
            'slug' => 'centralidade-de-cristo-vida-comunitaria',
            'excerpt' => 'Reflexão sobre os fundamentos eclesiásticos da renovação espiritual.',
            'content' => 'Conteúdo completo do artigo sobre Cristo.',
            'reading_time' => 6,
            'status' => 'published',
            'published_at' => now()->subDays(3),
        ]);

        Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Pastoreamento e Saúde Emocional do Líder',
            'slug' => 'pastoreamento-saude-emocional-lider',
            'excerpt' => 'Cuidando de quem cuida do rebanho em Goiás.',
            'content' => 'Conteúdo completo sobre pastoreamento.',
            'reading_time' => 5,
            'status' => 'published',
            'published_at' => now()->subDays(2),
        ]);

        Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Avivamento Bíblico e Missões no Século XXI',
            'slug' => 'avivamento-biblico-missoes-seculo-xxi',
            'excerpt' => 'Princípios para a expansão do Reino de Deus com integridade doutrinária.',
            'content' => 'Conteúdo completo sobre avivamento.',
            'reading_time' => 7,
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Artigos e Reflexões Pastorais');
        $response->assertSee('Avivamento Bíblico e Missões no Século XXI');
        $response->assertSee('Pastoreamento e Saúde Emocional do Líder');
        $response->assertSee('A Centralidade de Cristo na Vida Comunitária');
        $response->assertSee('Pr. Roberto Silveira');
        $response->assertSee('/artigos/avivamento-biblico-missoes-seculo-xxi');
        $response->assertSee('Discipulado nas Pequenas Comunidades');
        $response->assertDontSee('Artigo Antigo Fora do Top 4');
    }

    /**
     * Valida que artigos em rascunho ou com agendamento futuro são ocultados da Home (AC 3.4).
     */
    public function test_home_hides_draft_or_future_articles(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor([
            'name' => 'Pr. Carlos Mendes',
            'slug' => 'pr-carlos-mendes',
        ]);

        Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Artigo Rascunho Não Publicado',
            'slug' => 'artigo-rascunho',
            'excerpt' => 'Resumo rascunho.',
            'content' => 'Conteúdo.',
            'status' => 'draft',
            'published_at' => now()->subDay(),
        ]);

        Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Artigo com Data Futura',
            'slug' => 'artigo-futuro',
            'excerpt' => 'Resumo futuro.',
            'content' => 'Conteúdo.',
            'status' => 'published',
            'published_at' => now()->addDays(2),
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Artigo Rascunho Não Publicado');
        $response->assertDontSee('Artigo com Data Futura');
    }

    /**
     * Valida que os banners de destaque para o SETEBAN-GO e Eventos Anuais são exibidos (AC 3.5).
     */
    public function test_home_renders_institutional_banners_for_seteban_and_events(): void
    {
        $this->withoutVite();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('SETEBAN-GO');
        $response->assertSee('Seminário Teológico');
        $response->assertSee('Formação pastoral');
        $response->assertDontSee('Bacharelado');
        $response->assertSee('Assembleia Geral');
        $response->assertSee('Eventos Anuais');
    }

    /**
     * Valida que a página inicial renderiza graciosamente com mensagens de empty state
     * quando não houver notícias nem artigos cadastrados.
     */
    public function test_home_handles_empty_news_and_articles_gracefully(): void
    {
        $this->withoutVite();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Nenhuma notícia publicada no momento.');
        $response->assertSee('Nenhum artigo publicado no momento.');
    }

    /**
     * Valida que o DatabaseSeeder popula o banco com dados iniciais e a Home renderiza com sucesso.
     */
    public function test_database_seeder_populates_initial_home_content(): void
    {
        $this->withoutVite();

        $this->seed();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Inscrições abertas para delegados da 42ª Assembleia Geral Ordinária');
        $response->assertSee('Pr. Roberto Silveira');
        $response->assertSee('Diretoria Executiva divulga calendário oficial para o ano eclesiástico de 2026');
    }

    /**
     * Valida que todos os links de saída da Home (/quem-somos, /igrejas, /noticias, /artigos, slugs)
     * possuem rotas registradas e respondem com HTTP 200 (Zero 404s).
     */
    public function test_all_home_call_to_action_and_feed_links_respond_with_http_ok(): void
    {
        $this->withoutVite();

        News::factory()->create([
            'title' => 'Comunicado Oficial 2026',
            'slug' => 'comunicado-oficial-2026',
            'category' => 'Comunicado Oficial',
            'excerpt' => 'Resumo do comunicado oficial.',
            'content' => 'Conteúdo do comunicado oficial.',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $author = $this->createAuthor([
            'name' => 'Pr. Roberto Silveira',
            'slug' => 'pr-roberto-silveira',
        ]);

        Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Reflexão Bíblica Pastoral',
            'slug' => 'reflexao-biblica-pastoral',
            'category' => 'Teologia',
            'excerpt' => 'Resumo da reflexão bíblica pastoral.',
            'content' => 'Conteúdo da reflexão bíblica pastoral.',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $routesToTest = [
            '/quem-somos',
            '/igrejas',
            '/noticias',
            '/noticias/comunicado-oficial-2026',
            '/artigos',
            '/artigos/reflexao-biblica-pastoral',
        ];

        foreach ($routesToTest as $uri) {
            $response = $this->get($uri);
            $response->assertStatus(200);
        }
    }

    /**
     * Valida que o banner de eventos possui o id="eventos" como âncora válida na página.
     */
    public function test_events_anchor_target_exists_on_home_page(): void
    {
        $this->withoutVite();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('id="eventos"', false);
    }

    /**
     * Valida que, entre avisos de mesma prioridade, vence o de vigência mais recente (starts_at).
     */
    public function test_urgent_banner_prioritizes_latest_starts_at_when_multiple_are_active(): void
    {
        $this->withoutVite();

        UrgentNotice::factory()->create([
            'title' => 'Aviso Antigo',
            'content' => 'Mensagem do aviso mais antigo.',
            'priority' => 'normal',
            'is_active' => true,
            'starts_at' => now()->subDays(5),
        ]);

        UrgentNotice::factory()->create([
            'title' => 'Aviso Recente',
            'content' => 'Mensagem do aviso mais recente em vigor.',
            'priority' => 'normal',
            'is_active' => true,
            'starts_at' => now()->subHour(),
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Mensagem do aviso mais recente em vigor.');
        $response->assertDontSee('Mensagem do aviso mais antigo.');
    }

    /**
     * Valida que o aviso de maior prioridade vence, mesmo sendo mais antigo (urgent > high > normal).
     */
    public function test_urgent_banner_prioritizes_highest_priority(): void
    {
        $this->withoutVite();

        UrgentNotice::factory()->create(['content' => 'Aviso urgente antigo.', 'priority' => 'urgent', 'starts_at' => now()->subDays(3)]);
        UrgentNotice::factory()->create(['content' => 'Aviso alto recente.', 'priority' => 'high', 'starts_at' => now()->subHour()]);
        UrgentNotice::factory()->create(['content' => 'Aviso normal recente.', 'priority' => 'normal', 'starts_at' => now()->subMinute()]);

        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Aviso urgente antigo.')
            ->assertDontSee('Aviso alto recente.')
            ->assertDontSee('Aviso normal recente.');
    }

    /**
     * Valida o mapeamento de prioridade do painel para o estilo visual do banner.
     */
    #[DataProvider('noticePriorityProvider')]
    public function test_urgent_banner_maps_priority_to_banner_type(string $priority, string $expectedClass): void
    {
        $this->withoutVite();

        UrgentNotice::factory()->create(['priority' => $priority]);

        $this->get('/')->assertStatus(200)->assertSee($expectedClass, false);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function noticePriorityProvider(): array
    {
        return [
            'normal → info' => ['normal', 'bg-cbn-navy'],
            'high → warning' => ['high', 'from-cbn-gold-dark'],
            'urgent → critical' => ['urgent', 'bg-cbn-red'],
        ];
    }

    /**
     * Valida que o HTML do aviso (não sanitizado pelo painel) é exibido apenas como texto.
     */
    public function test_urgent_banner_renders_notice_content_as_plain_text(): void
    {
        $this->withoutVite();

        UrgentNotice::factory()->create([
            'content' => '<p>Culto <strong>adiado</strong>.</p><script>alert("xss")</script>',
            'action_url' => 'javascript:alert(1)',
            'action_label' => null,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Culto adiado.');
        $response->assertDontSee('<strong>adiado</strong>', false);
        $response->assertDontSee('alert("xss")', false);
        $response->assertDontSee('javascript:alert(1)', false);
    }

    /**
     * Valida que o rótulo do link cai em "Saiba mais" quando o painel não o preencheu.
     */
    public function test_urgent_banner_link_label_falls_back_to_saiba_mais(): void
    {
        $this->withoutVite();

        UrgentNotice::factory()->create(['action_url' => 'https://cbngo.org.br/agenda', 'action_label' => null]);

        $this->get('/')
            ->assertStatus(200)
            ->assertSee('https://cbngo.org.br/agenda')
            ->assertSee('Saiba mais');
    }

    /**
     * A seção de projetos sociais só aparece com projetos publicados (P.5).
     */
    public function test_home_hides_social_projects_section_without_published_projects(): void
    {
        $this->withoutVite();

        SocialProject::factory()->draft()->create(['title' => 'Projeto em Rascunho']);

        $response = $this->get('/');

        $response->assertDontSee('id="projetos-sociais"', false);
        $response->assertDontSee('Projeto em Rascunho');
    }

    /**
     * A home mostra até 3 projetos, com os destaques primeiro (P.5).
     */
    public function test_home_renders_up_to_three_social_projects_featured_first(): void
    {
        $this->withoutVite();

        SocialProject::factory()->count(3)->create(['published_at' => now()->subHour()]);
        SocialProject::factory()->featured()->create(['title' => 'Projeto Destaque Antigo', 'published_at' => now()->subMonth()]);

        $response = $this->get('/');
        $html = (string) $response->getContent();

        $response->assertSee('id="projetos-sociais"', false);
        $response->assertSee('Ver todos os projetos');
        $response->assertSee('href="'.config('cbngo.panel_url').'"', false);
        $response->assertDontSee('https://admin.cbngo.com.br', false);
        $this->assertSame(3, substr_count($html, 'data-testid="project-tile"'));
        // O destaque abre o mosaico (primeiro bloco, o maior).
        $this->assertLessThan(
            strpos($html, 'data-testid="project-tile"', strpos($html, 'data-testid="project-tile"') + 1),
            strpos($html, 'Projeto Destaque Antigo')
        );
    }

    /**
     * A faixa de projetos mostra números reais: em andamento, igrejas envolvidas e pessoas atendidas.
     */
    public function test_home_social_projects_band_shows_real_impact_numbers(): void
    {
        $this->withoutVite();

        $church = Church::factory()->create();
        SocialProject::factory()->forChurch($church)->create(['beneficiaries_count' => 1200]);
        SocialProject::factory()->forChurch($church)->create(['beneficiaries_count' => 300]);
        SocialProject::factory()->create(['beneficiaries_count' => 50]);
        SocialProject::factory()->ended()->create(['beneficiaries_count' => 999]);
        SocialProject::factory()->draft()->create(['beneficiaries_count' => 5000]);

        $html = (string) $this->get('/')->getContent();

        preg_match('#<dl[^>]*data-testid="project-stats"[^>]*>(.*?)</dl>#s', $html, $stats);
        $this->assertNotEmpty($stats);
        $this->assertMatchesRegularExpression('#Projetos em andamento</dt>\s*<dd[^>]*>3</dd>#', $stats[1]);
        $this->assertMatchesRegularExpression('#Igrejas envolvidas</dt>\s*<dd[^>]*>2</dd>#', $stats[1]);
        $this->assertMatchesRegularExpression('#Pessoas atendidas</dt>\s*<dd[^>]*>1.550</dd>#', $stats[1]);
        $this->assertStringContainsString(e(route('projetos-sociais.index', ['categoria' => 'educacao'])), $html);
    }

    /**
     * Notícias em layout editorial e artigos com citação em destaque (seções com layouts diferentes).
     */
    public function test_home_sections_use_editorial_news_and_quoted_article_layouts(): void
    {
        $this->withoutVite();

        News::factory()->count(4)->create();
        $author = Author::factory()->create();
        Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'O Pastor e a Família',
            'excerpt' => 'A eficácia do ministério repousa na saúde do lar.',
            'published_at' => now()->subHour(),
        ]);

        $response = $this->get('/');
        $html = (string) $response->getContent();

        preg_match('#<section[^>]*id="noticias"[^>]*>(.*?)</section>#s', $html, $news);
        $this->assertSame(3, substr_count($news[1], '<li class="py-5'));
        $this->assertStringContainsString('Mais notícias das igrejas', $news[1]);

        preg_match('#<section[^>]*id="artigos"[^>]*>(.*?)</section>#s', $html, $articles);
        $this->assertStringContainsString('<blockquote', $articles[1]);
        $this->assertStringContainsString('A eficácia do ministério repousa na saúde do lar.', $articles[1]);
        $this->assertStringContainsString(route('autores.show', $author->slug), $articles[1]);
    }
}
