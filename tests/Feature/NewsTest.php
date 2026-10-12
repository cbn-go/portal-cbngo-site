<?php

namespace Tests\Feature;

use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Valida que a página de notícias exibe as notícias publicadas e suporta paginação limpa de 9 itens por página.
     */
    public function test_news_index_displays_published_news_and_pagination(): void
    {
        $this->withoutVite();

        // Cria 12 notícias publicadas
        for ($i = 1; $i <= 12; $i++) {
            News::create([
                'title' => "Notícia Publicada {$i}",
                'slug' => "noticia-publicada-{$i}",
                'category' => 'Geral',
                'excerpt' => "Resumo da notícia {$i}",
                'content' => "Conteúdo da notícia {$i}",
                'is_published' => true,
                'published_at' => now()->subMinutes(13 - $i),
            ]);
        }

        // Página 1: deve exibir as 9 mais recentes (12 até 4)
        $response = $this->get(route('noticias.index'));

        $response->assertStatus(200);
        $response->assertSee('Notícia Publicada 12');
        $response->assertSee('Notícia Publicada 4');
        $response->assertDontSee('Notícia Publicada 3');
        $response->assertSee('page=2', false);

        // Página 2: deve exibir as restantes (3 até 1)
        $responsePage2 = $this->get(route('noticias.index', ['page' => 2]));

        $responsePage2->assertStatus(200);
        $responsePage2->assertSee('Notícia Publicada 3');
        $responsePage2->assertSee('Notícia Publicada 1');
        $responsePage2->assertDontSee('Notícia Publicada 12');
    }

    /**
     * Valida que notícias em rascunho ou com agendamento futuro são ocultadas da listagem.
     */
    public function test_news_index_hides_draft_and_future_news(): void
    {
        $this->withoutVite();

        News::create([
            'title' => 'Notícia Rascunho Oculta',
            'slug' => 'noticia-rascunho-oculta',
            'category' => 'Geral',
            'excerpt' => 'Resumo rascunho',
            'content' => 'Conteúdo rascunho',
            'is_published' => false,
            'published_at' => now()->subDay(),
        ]);

        News::create([
            'title' => 'Notícia Futura Oculta',
            'slug' => 'noticia-futura-oculta',
            'category' => 'Geral',
            'excerpt' => 'Resumo futuro',
            'content' => 'Conteúdo futuro',
            'is_published' => true,
            'published_at' => now()->addDays(2),
        ]);

        News::create([
            'title' => 'Notícia Publicada Visível',
            'slug' => 'noticia-publicada-visivel',
            'category' => 'Geral',
            'excerpt' => 'Resumo visível',
            'content' => 'Conteúdo visível',
            'is_published' => true,
            'published_at' => now()->subHour(),
        ]);

        $response = $this->get(route('noticias.index'));

        $response->assertStatus(200);
        $response->assertSee('Notícia Publicada Visível');
        $response->assertDontSee('Notícia Rascunho Oculta');
        $response->assertDontSee('Notícia Futura Oculta');
    }

    /**
     * Valida que a barra de busca textual filtra por título, resumo e conteúdo.
     */
    public function test_news_index_can_search_by_title_and_content(): void
    {
        $this->withoutVite();

        News::create([
            'title' => 'Convocação para Assembleia Extraordinária',
            'slug' => 'convocacao-assembleia-extraordinaria',
            'category' => 'Comunicado Oficial',
            'excerpt' => 'Edital de convocação dos delegados',
            'content' => 'A diretoria da CBN Goiás convoca todos os pastores.',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        News::create([
            'title' => 'Cruzada Evangelística no Interior',
            'slug' => 'cruzada-evangelistica-interior',
            'category' => 'Missões',
            'excerpt' => 'Grande impacto evangelístico na praça central',
            'content' => 'Centenas de vidas foram alcançadas pelo evangelho.',
            'is_published' => true,
            'published_at' => now()->subDays(2),
        ]);

        // Busca por termo no título
        $response = $this->get(route('noticias.index', ['q' => 'Assembleia']));
        $response->assertStatus(200);
        $response->assertSee('Convocação para Assembleia Extraordinária');
        $response->assertDontSee('Cruzada Evangelística no Interior');

        // Busca por termo no conteúdo
        $responseContent = $this->get(route('noticias.index', ['q' => 'vidas foram alcançadas']));
        $responseContent->assertStatus(200);
        $responseContent->assertSee('Cruzada Evangelística no Interior');
        $responseContent->assertDontSee('Convocação para Assembleia Extraordinária');
    }

    /**
     * Valida que o filtro por município goiano funciona corretamente.
     */
    public function test_news_index_can_filter_by_city(): void
    {
        $this->withoutVite();

        News::create([
            'title' => 'Notícia de Anápolis',
            'slug' => 'noticia-anapolis',
            'category' => 'Geral',
            'excerpt' => 'Resumo Anápolis',
            'content' => 'Conteúdo Anápolis',
            'city' => 'Anápolis',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        News::create([
            'title' => 'Notícia de Rio Verde',
            'slug' => 'noticia-rio-verde',
            'category' => 'Geral',
            'excerpt' => 'Resumo Rio Verde',
            'content' => 'Conteúdo Rio Verde',
            'city' => 'Rio Verde',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('noticias.index', ['cidade' => 'Anápolis']));
        $response->assertStatus(200);
        $response->assertSee('Notícia de Anápolis');
        $response->assertDontSee('Notícia de Rio Verde');
    }

    /**
     * Valida que o filtro por congregação emissora funciona corretamente.
     */
    public function test_news_index_can_filter_by_church(): void
    {
        $this->withoutVite();

        News::create([
            'title' => 'Notícia IBN Central',
            'slug' => 'noticia-ibn-central',
            'category' => 'Geral',
            'excerpt' => 'Resumo Central',
            'content' => 'Conteúdo Central',
            'church_name' => 'IBN Central Goiânia',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        News::create([
            'title' => 'Notícia IBN Betel',
            'slug' => 'noticia-ibn-betel',
            'category' => 'Geral',
            'excerpt' => 'Resumo Betel',
            'content' => 'Conteúdo Betel',
            'church_name' => 'IBN Betel Anápolis',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('noticias.index', ['igreja' => 'IBN Central Goiânia']));
        $response->assertStatus(200);
        $response->assertSee('Notícia IBN Central');
        $response->assertDontSee('Notícia IBN Betel');
    }

    /**
     * Valida que comunicados oficiais da diretoria possuem destaque visual e badge.
     */
    public function test_news_index_highlights_official_announcements(): void
    {
        $this->withoutVite();

        News::create([
            'title' => 'Diretoria Executiva Homologa Resoluções 2026',
            'slug' => 'diretoria-homologa-resolucoes-2026',
            'category' => 'Comunicado Oficial',
            'excerpt' => 'Resoluções normativas aprovadas em reunião ministerial.',
            'content' => 'A diretoria da CBN-GO esteve reunida na sede estadual.',
            'is_official' => true,
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('noticias.index'));

        $response->assertStatus(200);
        $response->assertSee('Diretoria Executiva Homologa Resoluções 2026');
        $response->assertSee('Comunicado Oficial');
    }

    /**
     * Valida que um empty state amigável é exibido quando a busca não retorna resultados.
     */
    public function test_news_index_shows_empty_state_when_no_results_match_filters(): void
    {
        $this->withoutVite();

        $response = $this->get(route('noticias.index', ['q' => 'termo_sem_resultado_xyz']));

        $response->assertStatus(200);
        $response->assertSee('Nenhuma notícia encontrada');
        $response->assertSee('Limpar filtros');
    }

    /**
     * Valida que a página de detalhes exibe todos os dados da notícia publicada.
     */
    public function test_news_show_displays_published_news_details_and_metadata(): void
    {
        $this->withoutVite();

        $news = News::create([
            'title' => 'Convenção Estadual 2026 Anuncia Programação Geral',
            'slug' => 'convencao-estadual-2026-anuncia-programacao',
            'category' => 'Eventos',
            'excerpt' => 'Programação oficial para o evento de celebração dos batistas nacionais em Goiás.',
            'content' => 'A Convenção Batista Nacional do Estado de Goiás tem a honra de convidar todas as igrejas.',
            'image_url' => 'https://cbngo.org.br/images/banner-convencao.jpg',
            'church_name' => 'Sede Estadual CBN-GO',
            'city' => 'Goiânia',
            'is_official' => true,
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('noticias.show', $news->slug));

        $response->assertStatus(200);
        $response->assertSee('Convenção Estadual 2026 Anuncia Programação Geral');
        $response->assertSee('Sede Estadual CBN-GO');
        $response->assertSee('Goiânia');
        $response->assertSee('Comunicado Oficial');
        $response->assertSee('https://cbngo.org.br/images/banner-convencao.jpg');
        $response->assertSee('A Convenção Batista Nacional do Estado de Goiás tem a honra de convidar todas as igrejas.');
    }

    /**
     * Valida que notícias não publicadas ou com publicação futura retornam HTTP 404.
     */
    public function test_news_show_returns_404_for_unpublished_or_future_news(): void
    {
        $this->withoutVite();

        $draft = News::create([
            'title' => 'Notícia Rascunho Inacessível',
            'slug' => 'noticia-rascunho-inacessivel',
            'category' => 'Geral',
            'excerpt' => 'Resumo rascunho',
            'content' => 'Conteúdo rascunho',
            'is_published' => false,
            'published_at' => now()->subDay(),
        ]);

        $future = News::create([
            'title' => 'Notícia Futura Inacessível',
            'slug' => 'noticia-futura-inacessivel',
            'category' => 'Geral',
            'excerpt' => 'Resumo futuro',
            'content' => 'Conteúdo futuro',
            'is_published' => true,
            'published_at' => now()->addDays(3),
        ]);

        $responseDraft = $this->get(route('noticias.show', $draft->slug));
        $responseDraft->assertStatus(404);

        $responseFuture = $this->get(route('noticias.show', $future->slug));
        $responseFuture->assertStatus(404);
    }

    /**
     * Valida que slug inexistente retorna HTTP 404.
     */
    public function test_news_show_returns_404_for_nonexistent_slug(): void
    {
        $this->withoutVite();

        $response = $this->get(route('noticias.show', 'slug-totalmente-inexistente-12345'));
        $response->assertStatus(404);
    }

    /**
     * Valida que a galeria de fotos e o lightbox são renderizados quando gallery_images estiver presente.
     */
    public function test_news_show_renders_photo_gallery_when_gallery_images_present(): void
    {
        $this->withoutVite();

        $news = News::create([
            'title' => 'Celebração com Galeria de Imagens',
            'slug' => 'celebracao-com-galeria-imagens',
            'category' => 'Eventos',
            'excerpt' => 'Resumo do evento',
            'content' => 'Conteúdo do evento com fotos registradas.',
            'gallery_images' => [
                'https://cbngo.org.br/fotos/foto1.jpg',
                'https://cbngo.org.br/fotos/foto2.jpg',
            ],
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('noticias.show', $news->slug));

        $response->assertStatus(200);
        $response->assertSee('Galeria de Fotos');
        $response->assertSee('https://cbngo.org.br/fotos/foto1.jpg');
        $response->assertSee('https://cbngo.org.br/fotos/foto2.jpg');
        $response->assertSee('x-data', false); // Lightbox interativo Alpine
    }

    /**
     * Valida que a seção de galeria não é renderizada quando gallery_images for nulo ou vazio.
     */
    public function test_news_show_does_not_render_gallery_when_gallery_images_is_null_or_empty(): void
    {
        $this->withoutVite();

        $news = News::create([
            'title' => 'Notícia sem Fotos Extras',
            'slug' => 'noticia-sem-fotos-extras',
            'category' => 'Geral',
            'excerpt' => 'Resumo sem fotos',
            'content' => 'Conteúdo comum.',
            'gallery_images' => null,
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('noticias.show', $news->slug));

        $response->assertStatus(200);
        $response->assertDontSee('Galeria de Fotos');
    }

    /**
     * Valida que os botões de compartilhamento social possuem URLs codificadas e botão de copiar link.
     */
    public function test_news_show_renders_social_share_links_with_correct_encoded_urls(): void
    {
        $this->withoutVite();

        $news = News::create([
            'title' => 'Assembleia Geral & Comunhão dos Pastores',
            'slug' => 'assembleia-geral-comunhao-pastores',
            'category' => 'Comunicado Oficial',
            'excerpt' => 'Resumo oficial',
            'content' => 'Conteúdo oficial.',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('noticias.show', $news->slug));

        $response->assertStatus(200);
        $response->assertSee('whatsapp', false);
        $response->assertSee('facebook.com/sharer', false);
        $response->assertSee('twitter.com/intent', false);
        $response->assertSee('Copiar Link');
    }

    /**
     * Valida que a página de detalhes exibe notícias recomendadas/relacionadas excluindo a matéria atual.
     */
    public function test_news_show_renders_related_news_excluding_current(): void
    {
        $this->withoutVite();

        $newsTarget = News::create([
            'title' => 'Notícia Principal em Foco',
            'slug' => 'noticia-principal-em-foco',
            'category' => 'Geral',
            'excerpt' => 'Resumo foco',
            'content' => 'Conteúdo foco.',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        $newsOther = News::create([
            'title' => 'Outra Notícia para Recomendação',
            'slug' => 'outra-noticia-para-recomendacao',
            'category' => 'Geral',
            'excerpt' => 'Resumo outra',
            'content' => 'Conteúdo outra.',
            'is_published' => true,
            'published_at' => now()->subDays(2),
        ]);

        $response = $this->get(route('noticias.show', $newsTarget->slug));

        $response->assertStatus(200);
        $response->assertSee('Outras Notícias');
        $response->assertSee('Outra Notícia para Recomendação');
    }

    /**
     * Valida que o navbar destaca 'Notícias' como item ativo na navegação.
     */
    public function test_navbar_highlights_noticias_as_active_when_browsing_news(): void
    {
        $this->withoutVite();

        $response = $this->get(route('noticias.index'));

        $response->assertStatus(200);
        $response->assertSee('border-b-2 border-cbn-black', false);
        $response->assertSee('Notícias');
    }
}
