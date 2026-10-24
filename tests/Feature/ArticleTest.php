<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Author;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Valida que a página de artigos exibe os artigos publicados e suporta paginação limpa de 9 itens por página.
     */
    public function test_articles_index_displays_published_articles_and_pagination(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor([
            'name' => 'Pr. Roberto Silveira',
            'slug' => 'pr-roberto-silveira',
            'pastoral_title' => 'Pastor e Teólogo',
        ]);

        for ($i = 1; $i <= 12; $i++) {
            Article::factory()->create([
                'user_id' => $author->user_id,
                'title' => "Artigo Teológico {$i}",
                'slug' => "artigo-teologico-{$i}",
                'category' => 'Teologia',
                'excerpt' => "Resumo do artigo {$i}",
                'content' => "Conteúdo do artigo {$i}",
                'reading_time' => 5,
                'status' => 'published',
                'published_at' => now()->subMinutes(13 - $i),
            ]);
        }

        // Página 1: deve exibir os 9 mais recentes
        $response = $this->get(route('artigos.index'));

        $response->assertStatus(200);
        $response->assertSee('Artigo Teológico 12');
        $response->assertSee('Artigo Teológico 4');
        $response->assertDontSee('Artigo Teológico 3');
        $response->assertSee('page=2', false);

        // Página 2: deve exibir os 3 restantes
        $responsePage2 = $this->get(route('artigos.index', ['page' => 2]));

        $responsePage2->assertStatus(200);
        $responsePage2->assertSee('Artigo Teológico 3');
        $responsePage2->assertSee('Artigo Teológico 1');
        $responsePage2->assertDontSee('Artigo Teológico 12');
    }

    /**
     * Valida que artigos em rascunho ou com agendamento futuro são ocultados da listagem.
     */
    public function test_articles_index_hides_draft_and_future_articles(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor([
            'name' => 'Pr. Carlos Mendes',
            'slug' => 'pr-carlos-mendes',
            'pastoral_title' => 'Professor de Hermenêutica',
        ]);

        Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Artigo Rascunho Oculto',
            'slug' => 'artigo-rascunho-oculto',
            'category' => 'Teologia',
            'excerpt' => 'Resumo rascunho',
            'content' => 'Conteúdo rascunho',
            'status' => 'draft',
            'published_at' => now()->subDay(),
        ]);

        Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Artigo Futuro Oculto',
            'slug' => 'artigo-futuro-oculto',
            'category' => 'Teologia',
            'excerpt' => 'Resumo futuro',
            'content' => 'Conteúdo futuro',
            'status' => 'published',
            'published_at' => now()->addDays(2),
        ]);

        Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Artigo Publicado Visível',
            'slug' => 'artigo-publicado-visivel',
            'category' => 'Teologia',
            'excerpt' => 'Resumo visível',
            'content' => 'Conteúdo visível',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $response = $this->get(route('artigos.index'));

        $response->assertStatus(200);
        $response->assertSee('Artigo Publicado Visível');
        $response->assertDontSee('Artigo Rascunho Oculto');
        $response->assertDontSee('Artigo Futuro Oculto');
    }

    /**
     * Valida que a busca textual funciona por título, resumo e conteúdo.
     */
    public function test_articles_index_can_search_by_title_and_content(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor([
            'name' => 'Pr. Roberto Silveira',
            'slug' => 'pr-roberto-silveira',
        ]);

        Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Avivamento Bíblico e Oração',
            'slug' => 'avivamento-biblico-oracao',
            'category' => 'Teologia',
            'excerpt' => 'Princípios para a busca do Espírito Santo',
            'content' => 'A oração contínua gera avivamento duradouro.',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Liderança Servidora na Igreja Local',
            'slug' => 'lideranca-servidora-igreja',
            'category' => 'Liderança',
            'excerpt' => 'O exemplo cristocêntrico de autoridade',
            'content' => 'Pastores devem liderar com humildade de coração.',
            'status' => 'published',
            'published_at' => now()->subDays(2),
        ]);

        // Busca por termo no título
        $responseTitle = $this->get(route('artigos.index', ['q' => 'Avivamento']));
        $responseTitle->assertStatus(200);
        $responseTitle->assertSee('Avivamento Bíblico e Oração');
        $responseTitle->assertDontSee('Liderança Servidora na Igreja Local');

        // Busca por termo no conteúdo
        $responseContent = $this->get(route('artigos.index', ['q' => 'humildade de coração']));
        $responseContent->assertStatus(200);
        $responseContent->assertSee('Liderança Servidora na Igreja Local');
        $responseContent->assertDontSee('Avivamento Bíblico e Oração');
    }

    /**
     * Valida que o filtro por autor funciona corretamente.
     */
    public function test_articles_index_can_filter_by_author(): void
    {
        $this->withoutVite();

        $authorA = $this->createAuthor([
            'name' => 'Pr. Roberto Silveira',
            'slug' => 'pr-roberto-silveira',
        ]);

        $authorB = $this->createAuthor([
            'name' => 'Pra. Helena Albuquerque',
            'slug' => 'pra-helena-albuquerque',
        ]);

        Article::factory()->create([
            'user_id' => $authorA->user_id,
            'title' => 'Artigo do Pastor Roberto',
            'slug' => 'artigo-pr-roberto',
            'category' => 'Teologia',
            'excerpt' => 'Resumo Roberto',
            'content' => 'Conteúdo Roberto',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        Article::factory()->create([
            'user_id' => $authorB->user_id,
            'title' => 'Artigo da Pastora Helena',
            'slug' => 'artigo-pra-helena',
            'category' => 'Família',
            'excerpt' => 'Resumo Helena',
            'content' => 'Conteúdo Helena',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('artigos.index', ['autor' => 'pr-roberto-silveira']));
        $response->assertStatus(200);
        $response->assertSee('Artigo do Pastor Roberto');
        $response->assertDontSee('Artigo da Pastora Helena');
    }

    /**
     * Valida que o filtro por categoria/tag temática funciona corretamente.
     */
    public function test_articles_index_can_filter_by_category_tag(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor([
            'name' => 'Pr. Roberto Silveira',
            'slug' => 'pr-roberto-silveira',
        ]);

        Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Reflexão sobre Doutrina Bíblica',
            'slug' => 'reflexao-doutrina-biblica',
            'category' => 'Teologia',
            'excerpt' => 'Resumo teológico',
            'content' => 'Conteúdo teológico',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'O Lar Cristão e os Desafios Atuais',
            'slug' => 'lar-cristao-desafios-atuais',
            'category' => 'Família',
            'excerpt' => 'Resumo familiar',
            'content' => 'Conteúdo familiar',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('artigos.index', ['categoria' => 'Família']));
        $response->assertStatus(200);
        $response->assertSee('O Lar Cristão e os Desafios Atuais');
        $response->assertDontSee('Reflexão sobre Doutrina Bíblica');
    }

    /**
     * Valida que um empty state amigável é exibido quando nenhum artigo corresponder aos filtros.
     */
    public function test_articles_index_shows_empty_state_when_no_results_match_filters(): void
    {
        $this->withoutVite();

        $response = $this->get(route('artigos.index', ['q' => 'termo_sem_resultado_999']));

        $response->assertStatus(200);
        $response->assertSee('Nenhum artigo encontrado');
        $response->assertSee('Limpar filtros');
    }

    /**
     * Valida que a paginação preserva os parâmetros da query string (busca e filtros).
     */
    public function test_articles_pagination_preserves_query_string_parameters(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor([
            'name' => 'Pr. Roberto Silveira',
            'slug' => 'pr-roberto-silveira',
        ]);

        for ($i = 1; $i <= 11; $i++) {
            Article::factory()->create([
                'user_id' => $author->user_id,
                'title' => "Artigo sobre Pastoreamento {$i}",
                'slug' => "artigo-pastoreamento-{$i}",
                'category' => 'Liderança',
                'excerpt' => "Resumo {$i}",
                'content' => "Conteúdo {$i}",
                'status' => 'published',
                'published_at' => now()->subMinutes(12 - $i),
            ]);
        }

        $response = $this->get(route('artigos.index', ['q' => 'Pastoreamento', 'categoria' => 'Liderança']));

        $response->assertStatus(200);
        $response->assertSee('q=Pastoreamento', false);
        $response->assertSee('categoria=Lideran%C3%A7a', false);
        $response->assertSee('page=2', false);
    }

    /**
     * Valida que a página de leitura exibe o conteúdo integral, tempo de leitura e metadados.
     */
    public function test_articles_show_displays_article_content_and_reading_time(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor([
            'name' => 'Pr. Roberto Silveira',
            'slug' => 'pr-roberto-silveira',
            'pastoral_title' => 'Presidente da CBN-GO',
            'bio' => 'Pastor com mais de 25 anos de ministério pastoral.',
        ]);

        $article = Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'A Importância da Oração no Ministério',
            'slug' => 'importancia-da-oracao-no-ministerio',
            'category' => 'Teologia',
            'excerpt' => 'Sem oração não há autoridade ministerial genuína.',
            'content' => "A oração é a respiração da alma cristã.\n\nSem ela, qualquer esforço eclesiástico é vão.",
            'reading_time' => 7,
            'status' => 'published',
            'published_at' => now()->subDays(2),
        ]);

        $response = $this->get(route('artigos.show', $article->slug));

        $response->assertStatus(200);
        $response->assertSee('A Importância da Oração no Ministério');
        $response->assertSee('7 min');
        $response->assertSee('Teologia');
        $response->assertSee('Pr. Roberto Silveira');
        $response->assertSee('A oração é a respiração da alma cristã.');
    }

    /**
     * Valida que o bloco do autor no rodapé exibe avatar, bio e links sociais.
     */
    public function test_articles_show_displays_author_block_with_bio_and_social_links(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor([
            'name' => 'Pr. Carlos Eduardo Mendes',
            'slug' => 'pr-carlos-eduardo-mendes',
            'pastoral_title' => 'Coordenador Teológico do SETEBAN-GO',
            'avatar' => 'https://cbngo.org.br/images/autores/pr-carlos.jpg',
            'bio' => 'Doutor em Teologia e professor de hermenêutica bíblica.',
            'social_links' => [
                'instagram' => 'https://instagram.com/prcarlosmendes',
                'email' => 'prcarlos@cbngo.org.br',
            ],
        ]);

        $article = Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Hermenêutica para Tempos de Crise',
            'slug' => 'hermeneutica-para-tempos-de-crise',
            'category' => 'Teologia',
            'excerpt' => 'Como interpretar com fidelidade o texto sagrado.',
            'content' => 'O texto bíblico nunca pode significar o que nunca significou.',
            'reading_time' => 6,
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('artigos.show', $article->slug));

        $response->assertStatus(200);
        $response->assertSee('Sobre o Autor');
        $response->assertSee('Pr. Carlos Eduardo Mendes');
        $response->assertSee('Coordenador Teológico do SETEBAN-GO');
        $response->assertSee('Doutor em Teologia e professor de hermenêutica bíblica.');
        $response->assertSee('https://instagram.com/prcarlosmendes');
        $response->assertSee('prcarlos@cbngo.org.br');
        $response->assertSee('https://cbngo.org.br/images/autores/pr-carlos.jpg');
    }

    /**
     * Valida que artigos não publicados ou futuros retornam HTTP 404.
     */
    public function test_articles_show_returns_404_for_unpublished_or_future_article(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor([
            'name' => 'Pr. Roberto Silveira',
            'slug' => 'pr-roberto-silveira',
        ]);

        $draft = Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Artigo Rascunho',
            'slug' => 'artigo-rascunho-404',
            'category' => 'Teologia',
            'excerpt' => 'Resumo rascunho',
            'content' => 'Conteúdo rascunho',
            'status' => 'draft',
            'published_at' => now()->subDay(),
        ]);

        $future = Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Artigo Futuro',
            'slug' => 'artigo-futuro-404',
            'category' => 'Teologia',
            'excerpt' => 'Resumo futuro',
            'content' => 'Conteúdo futuro',
            'status' => 'published',
            'published_at' => now()->addDays(3),
        ]);

        $responseDraft = $this->get(route('artigos.show', $draft->slug));
        $responseDraft->assertStatus(404);

        $responseFuture = $this->get(route('artigos.show', $future->slug));
        $responseFuture->assertStatus(404);
    }

    /**
     * Valida que slug inexistente retorna HTTP 404.
     */
    public function test_articles_show_returns_404_for_nonexistent_slug(): void
    {
        $this->withoutVite();

        $response = $this->get(route('artigos.show', 'slug-artigo-inexistente-12345'));
        $response->assertStatus(404);
    }

    /**
     * Valida que os botões de compartilhamento social possuem URLs codificadas e botão de cópia com feedback.
     */
    public function test_articles_show_renders_social_share_links_and_copy_button(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor([
            'name' => 'Pr. Roberto Silveira',
            'slug' => 'pr-roberto-silveira',
        ]);

        $article = Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Teologia & Práxis na Igreja Contemporânea',
            'slug' => 'teologia-praxis-igreja-contemporanea',
            'category' => 'Teologia',
            'excerpt' => 'Resumo teológico',
            'content' => 'Conteúdo teológico',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('artigos.show', $article->slug));

        $response->assertStatus(200);
        $response->assertSee('whatsapp', false);
        $response->assertSee('facebook.com/sharer', false);
        $response->assertSee('twitter.com/intent', false);
        $response->assertSee('Copiar Link');
    }

    /**
     * Valida que a página de leitura exibe outros artigos recomendados excluindo o artigo atual.
     */
    public function test_articles_show_renders_recommended_articles_excluding_current(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor([
            'name' => 'Pr. Roberto Silveira',
            'slug' => 'pr-roberto-silveira',
        ]);

        $target = Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Artigo Principal em Leitura',
            'slug' => 'artigo-principal-em-leitura',
            'category' => 'Teologia',
            'excerpt' => 'Resumo principal',
            'content' => 'Conteúdo principal',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $other = Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Outro Artigo Pastoral Recomendado',
            'slug' => 'outro-artigo-pastoral-recomendado',
            'category' => 'Liderança',
            'excerpt' => 'Resumo recomendado',
            'content' => 'Conteúdo recomendado',
            'status' => 'published',
            'published_at' => now()->subDays(2),
        ]);

        $response = $this->get(route('artigos.show', $target->slug));

        $response->assertStatus(200);
        $response->assertSee('Outros Artigos Recomendados');
        $response->assertSee('Outro Artigo Pastoral Recomendado');
    }

    /**
     * Valida que o perfil público do autor pode ser acessado pelo slug textual.
     */
    public function test_author_profile_can_be_accessed_by_slug(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor([
            'name' => 'Pr. Roberto Silveira',
            'slug' => 'pr-roberto-silveira',
            'pastoral_title' => 'Pastor Titular e Presidente da CBN-GO',
            'bio' => 'Ministro do evangelho e bacharel em Teologia.',
        ]);

        $response = $this->get(route('autores.show', $author->slug));

        $response->assertStatus(200);
        $response->assertSee('Pr. Roberto Silveira');
        $response->assertSee('Pastor Titular e Presidente da CBN-GO');
        $response->assertSee('Ministro do evangelho e bacharel em Teologia.');
    }

    /**
     * Valida que o perfil público do autor pode ser acessado pelo ID numérico.
     */
    public function test_author_profile_can_be_accessed_by_id(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor([
            'name' => 'Pra. Helena Albuquerque',
            'slug' => 'pra-helena-albuquerque',
            'pastoral_title' => 'Líder de Missões Estaduais',
        ]);

        $response = $this->get(route('autores.show', $author->id));

        $response->assertStatus(200);
        $response->assertSee('Pra. Helena Albuquerque');
        $response->assertSee('Líder de Missões Estaduais');
    }

    /**
     * Valida que nenhum status diferente de "published" (nem publicado sem data) é exibido no portal.
     */
    public function test_articles_with_non_published_status_never_appear(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor(['slug' => 'autor-com-pendencias']);

        $hidden = [
            Article::factory()->byAuthor($author)->draft()->create(['title' => 'Oculto Rascunho']),
            Article::factory()->byAuthor($author)->pendingReview()->create(['title' => 'Oculto Pendente de Revisão']),
            Article::factory()->byAuthor($author)->archived()->create(['title' => 'Oculto Arquivado']),
            Article::factory()->byAuthor($author)->create(['title' => 'Oculto Sem Data', 'published_at' => null]),
        ];

        $index = $this->get(route('artigos.index'));
        $profile = $this->get(route('autores.show', $author->slug));
        $home = $this->get(route('home'));

        foreach ($hidden as $article) {
            $index->assertDontSee($article->title);
            $profile->assertDontSee($article->title);
            $home->assertDontSee($article->title);
            $this->get(route('artigos.show', $article->slug))->assertStatus(404);
        }
    }

    /**
     * Valida que o perfil de um usuário desativado no painel não é exibido nem aparece nos filtros.
     */
    public function test_author_of_inactive_user_is_hidden(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor(['name' => 'Pr. Usuário Desativado', 'slug' => 'pr-desativado', 'is_active' => false]);
        Article::factory()->byAuthor($author)->create();

        $this->get(route('autores.show', $author->slug))->assertStatus(404);
        $this->get(route('autores.show', $author->id))->assertStatus(404);
        $this->get(route('artigos.index'))->assertDontSee('<option value="pr-desativado"', false);
    }

    /**
     * Valida que o título pastoral cai em "Articulista CBN-GO" quando o painel não o preencheu.
     */
    public function test_author_role_title_falls_back_when_pastoral_title_is_empty(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor(['name' => 'Ir. Marta Lopes', 'pastoral_title' => null]);

        $this->get(route('autores.show', $author->slug))
            ->assertStatus(200)
            ->assertSee('Ir. Marta Lopes')
            ->assertSee('Articulista CBN-GO');
    }

    /**
     * Valida que capa do artigo e avatar do autor são montados a partir do MEDIA_URL.
     */
    public function test_article_cover_and_author_avatar_use_media_url(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor(['avatar' => 'authors/avatars/marta.jpg']);
        $article = Article::factory()->byAuthor($author)->create(['cover_image' => 'articles/covers/graca.jpg']);

        $this->get(route('artigos.show', $article->slug))
            ->assertStatus(200)
            ->assertSee('https://midia.test/articles/covers/graca.jpg')
            ->assertSee('https://midia.test/authors/avatars/marta.jpg');
    }

    /**
     * Valida que artigo de usuário sem perfil de autor ainda é exibido, atribuído à CBN Goiás.
     */
    public function test_article_without_author_profile_is_attributed_to_cbn_goias(): void
    {
        $this->withoutVite();

        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id, 'title' => 'Artigo Sem Perfil']);

        $this->get(route('artigos.index'))->assertStatus(200)->assertSee('Artigo Sem Perfil');
        $this->get(route('artigos.show', $article->slug))->assertStatus(200)->assertSee('CBN Goiás');
    }

    /**
     * Valida que identificador inexistente (slug ou ID) retorna HTTP 404.
     */
    public function test_author_profile_returns_404_for_invalid_slug_or_id(): void
    {
        $this->withoutVite();

        $responseSlug = $this->get(route('autores.show', 'autor-que-nao-existe'));
        $responseSlug->assertStatus(404);

        $responseId = $this->get(route('autores.show', 999999));
        $responseId->assertStatus(404);
    }

    /**
     * Valida que o perfil do autor lista os artigos publicados por ele com paginação.
     */
    public function test_author_profile_displays_bio_credentials_and_published_articles(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor([
            'name' => 'Pr. Roberto Silveira',
            'slug' => 'pr-roberto-silveira',
            'pastoral_title' => 'Pastor e Articulista',
            'bio' => 'Biografia detalhada do pastor Roberto.',
        ]);

        Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Artigo 1 do Pastor Roberto',
            'slug' => 'artigo-1-pastor-roberto',
            'category' => 'Teologia',
            'excerpt' => 'Resumo 1',
            'content' => 'Conteúdo 1',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Artigo 2 do Pastor Roberto',
            'slug' => 'artigo-2-pastor-roberto',
            'category' => 'Liderança',
            'excerpt' => 'Resumo 2',
            'content' => 'Conteúdo 2',
            'status' => 'published',
            'published_at' => now()->subDays(2),
        ]);

        $response = $this->get(route('autores.show', $author->slug));

        $response->assertStatus(200);
        $response->assertSee('Artigo 1 do Pastor Roberto');
        $response->assertSee('Artigo 2 do Pastor Roberto');
        $response->assertSee('2 artigos publicados');
    }

    /**
     * Valida que artigos em rascunho são ocultados do perfil do articulista.
     */
    public function test_author_profile_hides_draft_articles_from_author_feed(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor([
            'name' => 'Pr. Roberto Silveira',
            'slug' => 'pr-roberto-silveira',
        ]);

        Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Artigo Rascunho Não Publicado',
            'slug' => 'artigo-rascunho-invisivel',
            'category' => 'Teologia',
            'excerpt' => 'Resumo rascunho',
            'content' => 'Conteúdo rascunho',
            'status' => 'draft',
            'published_at' => now()->subDay(),
        ]);

        Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Artigo Publicado Visível',
            'slug' => 'artigo-publicado-visivel-autor',
            'category' => 'Teologia',
            'excerpt' => 'Resumo visível',
            'content' => 'Conteúdo visível',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('autores.show', $author->slug));

        $response->assertStatus(200);
        $response->assertSee('Artigo Publicado Visível');
        $response->assertDontSee('Artigo Rascunho Não Publicado');
    }

    /**
     * Valida que o navbar destaca 'Artigos' com aria-current="page" em /artigos e /artigos/{slug}.
     */
    public function test_navbar_highlights_artigos_as_active_when_browsing_articles(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor([
            'name' => 'Pr. Roberto Silveira',
            'slug' => 'pr-roberto-silveira',
        ]);

        $article = Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Artigo Teste Ativo',
            'slug' => 'artigo-teste-ativo',
            'category' => 'Teologia',
            'excerpt' => 'Resumo',
            'content' => 'Conteúdo',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        // 1. No feed /artigos
        $responseFeed = $this->get(route('artigos.index'));
        $responseFeed->assertStatus(200);
        $responseFeed->assertSee('href="'.route('artigos.index').'"', false);
        $responseFeed->assertSee('aria-current="page"', false);
        $responseFeed->assertDontSee('href="'.route('home').'"'."\n".'                        class="text-cbn-black font-semibold border-b-2 border-cbn-black', false);

        // 2. Na leitura /artigos/{slug}
        $responseShow = $this->get(route('artigos.show', $article->slug));
        $responseShow->assertStatus(200);
        $responseShow->assertSee('href="'.route('artigos.index').'"', false);
        $responseShow->assertSee('aria-current="page"', false);
        $responseShow->assertDontSee('href="'.route('home').'"'."\n".'                        class="text-cbn-black font-semibold border-b-2 border-cbn-black', false);

        // 3. No perfil do autor /autores/{slug}
        $responseAuthor = $this->get(route('autores.show', $author->slug));
        $responseAuthor->assertStatus(200);
        $responseAuthor->assertSee('href="'.route('artigos.index').'"', false);
        $responseAuthor->assertSee('aria-current="page"', false);
        $responseAuthor->assertDontSee('href="'.route('home').'"'."\n".'                        class="text-cbn-black font-semibold border-b-2 border-cbn-black', false);
    }

    /**
     * Valida que o perfil do autor pagina adequadamente com mais de 9 artigos e suporta navegação para page=2.
     */
    public function test_author_profile_paginates_articles_when_more_than_nine(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor([
            'name' => 'Pr. Roberto Silveira',
            'slug' => 'pr-roberto-silveira',
            'pastoral_title' => 'Pastor e Articulista',
        ]);

        for ($i = 1; $i <= 12; $i++) {
            Article::factory()->create([
                'user_id' => $author->user_id,
                'title' => "Artigo de Série {$i}",
                'slug' => "artigo-de-serie-{$i}",
                'category' => 'Teologia',
                'excerpt' => "Resumo {$i}",
                'content' => "Conteúdo {$i}",
                'reading_time' => 5,
                'status' => 'published',
                'published_at' => now()->subMinutes(13 - $i),
            ]);
        }

        // Página 1: exibe os 9 mais recentes (12 até 4), não exibe artigo 3, tem link de paginação com page=2
        $responsePage1 = $this->get(route('autores.show', $author->slug));
        $responsePage1->assertStatus(200);
        $responsePage1->assertSee('Artigo de Série 12');
        $responsePage1->assertSee('Artigo de Série 4');
        $responsePage1->assertDontSee('Artigo de Série 3');
        $responsePage1->assertSee('page=2', false);

        // Página 2: exibe os 3 restantes (3 até 1), não exibe artigo 12
        $responsePage2 = $this->get(route('autores.show', [$author->slug, 'page' => 2]));
        $responsePage2->assertStatus(200);
        $responsePage2->assertSee('Artigo de Série 3');
        $responsePage2->assertSee('Artigo de Série 1');
        $responsePage2->assertDontSee('Artigo de Série 12');
    }

    /**
     * Valida que esquemas maliciosos em social_links (ex: javascript:...) são descartados e links válidos são renderizados.
     */
    public function test_social_links_filter_disallowed_schemes_preventing_xss(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor([
            'name' => 'Pr. Daniel Pereira',
            'slug' => 'pr-daniel-pereira',
            'pastoral_title' => 'Pastor e Escritor',
            'bio' => 'Biografia de teste.',
            'social_links' => [
                'instagram' => 'https://instagram.com/prdaniel',
                'xss' => 'javascript:alert(document.cookie)',
                'data' => 'data:text/html,<script>alert(1)</script>',
                'email' => 'daniel@cbngo.org.br',
            ],
        ]);

        $article = Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'Artigo com Autor com Links Seguros',
            'slug' => 'artigo-com-autor-links-seguros',
            'category' => 'Teologia',
            'excerpt' => 'Resumo',
            'content' => 'Conteúdo',
            'reading_time' => 5,
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        // 1. No perfil do autor
        $responseAuthor = $this->get(route('autores.show', $author->slug));
        $responseAuthor->assertStatus(200);
        $responseAuthor->assertSee('https://instagram.com/prdaniel');
        $responseAuthor->assertSee('mailto:daniel@cbngo.org.br');
        $responseAuthor->assertDontSee('javascript:', false);
        $responseAuthor->assertDontSee('data:text/html', false);

        // 2. Na leitura do artigo (bloco do autor)
        $responseArticle = $this->get(route('artigos.show', $article->slug));
        $responseArticle->assertStatus(200);
        $responseArticle->assertSee('https://instagram.com/prdaniel');
        $responseArticle->assertSee('mailto:daniel@cbngo.org.br');
        $responseArticle->assertDontSee('javascript:', false);
        $responseArticle->assertDontSee('data:text/html', false);
    }
}
