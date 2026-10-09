<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Author;
use App\Models\News;
use App\Models\UrgentNotice;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        UrgentNotice::create([
            'title' => 'Comunicado de Assembleia Extraordinária',
            'message' => 'Convocação oficial para a Assembleia Geral da CBN-GO em Anápolis.',
            'link' => 'https://cbngo.org.br/edital-2026',
            'link_text' => 'Ler Edital',
            'type' => 'critical',
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(2),
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
        UrgentNotice::create([
            'title' => 'Aviso Inativo',
            'message' => 'Este aviso não deve aparecer porque está inativo.',
            'is_active' => false,
        ]);

        // Aviso expirado
        UrgentNotice::create([
            'title' => 'Aviso Expirado',
            'message' => 'Este aviso não deve aparecer porque já expirou.',
            'is_active' => true,
            'ends_at' => now()->subMinute(),
        ]);

        // Aviso futuro
        UrgentNotice::create([
            'title' => 'Aviso Futuro',
            'message' => 'Este aviso não deve aparecer porque ainda não entrou em vigência.',
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
     * Valida que a seção de notícias exibe as 3 mais recentes publicadas (AC 3.3).
     */
    public function test_home_renders_latest_published_news(): void
    {
        $this->withoutVite();

        News::create([
            'title' => 'Notícia Antiga Fora do Top 3',
            'slug' => 'noticia-antiga-fora-do-top-3',
            'category' => 'Geral',
            'excerpt' => 'Resumo da notícia antiga.',
            'content' => 'Conteúdo detalhado da notícia antiga.',
            'is_published' => true,
            'published_at' => now()->subDays(10),
        ]);

        News::create([
            'title' => 'Retiro Estadual de Pastores 2026',
            'slug' => 'retiro-estadual-de-pastores-2026',
            'category' => 'Eventos',
            'excerpt' => 'Encontro de comunhão e edificação pastoral em Pirenópolis.',
            'content' => 'Conteúdo detalhado do retiro.',
            'is_published' => true,
            'published_at' => now()->subDays(3),
        ]);

        News::create([
            'title' => 'Nova Congregação Inaugurada em Rio Verde',
            'slug' => 'nova-congregacao-inaugurada-em-rio-verde',
            'category' => 'Missões',
            'excerpt' => 'Avanço missionário da CBN Goiás no sudoeste do estado.',
            'content' => 'Conteúdo detalhado da inauguração.',
            'is_published' => true,
            'published_at' => now()->subDays(2),
        ]);

        News::create([
            'title' => 'Comunicado Oficial da Diretoria da CBN-GO',
            'slug' => 'comunicado-oficial-da-diretoria-cbn-go',
            'category' => 'Comunicado Oficial',
            'excerpt' => 'Orientações pastorais sobre o ano eclesiástico de 2026.',
            'content' => 'Conteúdo completo do comunicado.',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Últimas Notícias e Comunicados');
        $response->assertSee('Comunicado Oficial da Diretoria da CBN-GO');
        $response->assertSee('Nova Congregação Inaugurada em Rio Verde');
        $response->assertSee('Retiro Estadual de Pastores 2026');
        $response->assertSee('/noticias/comunicado-oficial-da-diretoria-cbn-go');
        $response->assertDontSee('Notícia Antiga Fora do Top 3');
    }

    /**
     * Valida que notícias em rascunho ou com agendamento futuro são ocultadas da Home (AC 3.3).
     */
    public function test_home_hides_draft_or_future_news(): void
    {
        $this->withoutVite();

        News::create([
            'title' => 'Notícia Não Publicada em Rascunho',
            'slug' => 'noticia-rascunho',
            'category' => 'Geral',
            'excerpt' => 'Resumo rascunho.',
            'content' => 'Conteúdo rascunho.',
            'is_published' => false,
            'published_at' => now()->subDay(),
        ]);

        News::create([
            'title' => 'Notícia com Publicação Agendada Futura',
            'slug' => 'noticia-futura',
            'category' => 'Geral',
            'excerpt' => 'Resumo futuro.',
            'content' => 'Conteúdo futuro.',
            'is_published' => true,
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

        $author = Author::create([
            'name' => 'Pr. Roberto Silveira',
            'slug' => 'pr-roberto-silveira',
            'role_title' => 'Pastor e Teólogo',
        ]);

        Article::create([
            'author_id' => $author->id,
            'title' => 'Artigo Antigo Fora do Top 3',
            'slug' => 'artigo-antigo-fora-top-3',
            'excerpt' => 'Resumo artigo antigo.',
            'content' => 'Conteúdo artigo antigo.',
            'reading_time' => 4,
            'is_published' => true,
            'published_at' => now()->subDays(15),
        ]);

        Article::create([
            'author_id' => $author->id,
            'title' => 'A Centralidade de Cristo na Vida Comunitária',
            'slug' => 'centralidade-de-cristo-vida-comunitaria',
            'excerpt' => 'Reflexão sobre os fundamentos eclesiásticos da renovação espiritual.',
            'content' => 'Conteúdo completo do artigo sobre Cristo.',
            'reading_time' => 6,
            'is_published' => true,
            'published_at' => now()->subDays(3),
        ]);

        Article::create([
            'author_id' => $author->id,
            'title' => 'Pastoreamento e Saúde Emocional do Líder',
            'slug' => 'pastoreamento-saude-emocional-lider',
            'excerpt' => 'Cuidando de quem cuida do rebanho em Goiás.',
            'content' => 'Conteúdo completo sobre pastoreamento.',
            'reading_time' => 5,
            'is_published' => true,
            'published_at' => now()->subDays(2),
        ]);

        Article::create([
            'author_id' => $author->id,
            'title' => 'Avivamento Bíblico e Missões no Século XXI',
            'slug' => 'avivamento-biblico-missoes-seculo-xxi',
            'excerpt' => 'Princípios para a expansão do Reino de Deus com integridade doutrinária.',
            'content' => 'Conteúdo completo sobre avivamento.',
            'reading_time' => 7,
            'is_published' => true,
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
        $response->assertDontSee('Artigo Antigo Fora do Top 3');
    }

    /**
     * Valida que artigos em rascunho ou com agendamento futuro são ocultados da Home (AC 3.4).
     */
    public function test_home_hides_draft_or_future_articles(): void
    {
        $this->withoutVite();

        $author = Author::create([
            'name' => 'Pr. Carlos Mendes',
            'slug' => 'pr-carlos-mendes',
        ]);

        Article::create([
            'author_id' => $author->id,
            'title' => 'Artigo Rascunho Não Publicado',
            'slug' => 'artigo-rascunho',
            'excerpt' => 'Resumo rascunho.',
            'content' => 'Conteúdo.',
            'is_published' => false,
            'published_at' => now()->subDay(),
        ]);

        Article::create([
            'author_id' => $author->id,
            'title' => 'Artigo com Data Futura',
            'slug' => 'artigo-futuro',
            'excerpt' => 'Resumo futuro.',
            'content' => 'Conteúdo.',
            'is_published' => true,
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
}
