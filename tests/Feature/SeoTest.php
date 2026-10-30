<?php

namespace Tests\Feature;

use App\Enums\ProjectCategory;
use App\Models\Article;
use App\Models\Church;
use App\Models\News;
use App\Models\SocialProject;
use App\Models\Statute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Valida que a home usa o título da marca, a descrição padrão e as tags OpenGraph do site (AC 8.1).
     */
    public function test_home_renders_brand_title_default_description_and_open_graph(): void
    {
        $this->withoutVite();

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('<title>CBN Goiás — Convenção Batista Nacional do Estado de Goiás</title>', false);
        $response->assertSee('<meta name="description" content="'.e(config('cbngo.seo.default_description')).'">', false);
        $response->assertSee('<link rel="canonical" href="'.url('/').'">', false);
        $response->assertSee('<meta property="og:type" content="website">', false);
        $response->assertSee('<meta property="og:site_name" content="CBN Goiás">', false);
        $response->assertSee('<meta property="og:locale" content="pt_BR">', false);
        $response->assertSee('<meta property="og:image" content="'.asset('images/brand/cbn-go-share.png').'">', false);
        $response->assertSee('<meta property="og:image:width" content="1200">', false);
        $response->assertSee('<meta property="og:image:height" content="630">', false);
        $response->assertSee('<meta property="og:image:alt" content="CBN Goiás — Convenção Batista Nacional do Estado de Goiás">', false);
        $response->assertSee('<meta name="twitter:card" content="summary_large_image">', false);
        $response->assertSee('<meta name="robots" content="index, follow">', false);
    }

    /**
     * Valida as metatags dinâmicas de uma notícia publicada: título, descrição, OpenGraph e Twitter Cards (AC 8.1).
     */
    public function test_news_page_renders_dynamic_meta_and_open_graph_tags(): void
    {
        $this->withoutVite();

        $news = News::factory()->create([
            'title' => 'Assembleia Estadual 2026',
            'slug' => 'assembleia-estadual-2026',
            'excerpt' => 'Pastores e líderes reunidos em Goiânia para a assembleia anual.',
            'featured_image' => 'news/featured/assembleia.jpg',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('noticias.show', $news->slug));

        $response->assertStatus(200);
        $response->assertSee('<title>Assembleia Estadual 2026 — CBN Goiás</title>', false);
        $response->assertDontSee('CBN Goiás - Assembleia', false);
        $response->assertSee('<meta name="description" content="Pastores e líderes reunidos em Goiânia para a assembleia anual.">', false);
        $response->assertSee('<link rel="canonical" href="'.route('noticias.show', $news->slug).'">', false);
        $response->assertSee('<meta property="og:type" content="article">', false);
        $response->assertSee('<meta property="og:title" content="Assembleia Estadual 2026">', false);
        $response->assertSee('<meta property="og:url" content="'.route('noticias.show', $news->slug).'">', false);
        // Capa no compartilhamento: variante cover (WebP 1600px), sem dimensões fixas.
        $response->assertSee('<meta property="og:image" content="https://midia.test/news/featured/assembleia-cover.webp">', false);
        $response->assertSee('<meta property="og:image:alt" content="Assembleia Estadual 2026">', false);
        $response->assertDontSee('og:image:width', false);
        $response->assertSee('<meta name="twitter:title" content="Assembleia Estadual 2026">', false);
        $response->assertSee('<meta name="twitter:image" content="https://midia.test/news/featured/assembleia-cover.webp">', false);
        $response->assertSee('<meta name="twitter:image:alt" content="Assembleia Estadual 2026">', false);
        $response->assertSee('<meta property="article:published_time" content="'.$news->published_at->toIso8601String().'">', false);
    }

    /**
     * Valida que notícias sem capa, ou com URL de imagem insegura, usam a imagem padrão de compartilhamento (Edge Cases 1 e 4).
     */
    public function test_news_without_image_falls_back_to_default_share_image(): void
    {
        $this->withoutVite();

        $withoutImage = News::factory()->create(['slug' => 'sem-capa', 'featured_image' => null]);
        $unsafeImage = News::factory()->create(['slug' => 'capa-insegura', 'featured_image' => 'javascript:alert(1)']);

        foreach ([$withoutImage, $unsafeImage] as $news) {
            $response = $this->get(route('noticias.show', $news->slug));

            $response->assertStatus(200);
            $response->assertSee('<meta property="og:image" content="'.asset('images/brand/cbn-go-share.png').'">', false);
            $response->assertSee('<meta property="og:image:width" content="1200">', false);
            $response->assertDontSee('content="javascript:', false);
        }
    }

    /**
     * Valida que a descrição remove HTML e é limitada a 160 caracteres (Edge Case 3).
     */
    public function test_meta_description_is_stripped_and_limited(): void
    {
        $this->withoutVite();

        $news = News::factory()->create([
            'slug' => 'resumo-longo',
            'excerpt' => '<strong>Destaque</strong> '.str_repeat('palavra ', 40),
        ]);

        $response = $this->get(route('noticias.show', $news->slug));

        preg_match('/<meta name="description" content="([^"]*)">/', $response->getContent(), $matches);

        $this->assertNotEmpty($matches);
        $this->assertStringStartsWith('Destaque palavra', $matches[1]);
        $this->assertStringNotContainsString('strong', $matches[1]);
        $this->assertLessThanOrEqual(160, mb_strlen(html_entity_decode($matches[1])));
    }

    /**
     * Valida as metatags de artigo, incluindo o autor e o avatar como imagem de compartilhamento (AC 8.1).
     */
    public function test_article_page_renders_article_meta_and_author(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor([
            'name' => 'Pr. Roberto Silveira',
            'slug' => 'pr-roberto-silveira',
            'avatar' => 'https://cdn.cbngo.org.br/autores/roberto.jpg',
        ]);

        $article = Article::factory()->create([
            'user_id' => $author->user_id,
            'title' => 'A Graça que Transforma',
            'slug' => 'a-graca-que-transforma',
            'excerpt' => 'Uma reflexão pastoral sobre a graça.',
        ]);

        $response = $this->get(route('artigos.show', $article->slug));

        $response->assertStatus(200);
        $response->assertSee('<title>A Graça que Transforma — CBN Goiás</title>', false);
        $response->assertSee('<meta name="description" content="Uma reflexão pastoral sobre a graça.">', false);
        $response->assertSee('<meta property="og:type" content="article">', false);
        $response->assertSee('<meta property="og:image" content="https://cdn.cbngo.org.br/autores/roberto.jpg">', false);
        $response->assertSee('<meta property="article:author" content="Pr. Roberto Silveira">', false);
    }

    /**
     * Valida que o canonical descarta filtros de busca, mas mantém a página da paginação (Edge Case 7).
     */
    public function test_canonical_ignores_filters_but_keeps_page(): void
    {
        $this->withoutVite();

        $this->get(route('noticias.index', ['q' => 'missões']))
            ->assertSee('<link rel="canonical" href="'.route('noticias.index').'">', false);

        $this->get(route('noticias.index', ['q' => 'missões', 'page' => 2]))
            ->assertSee('<link rel="canonical" href="'.route('noticias.index').'?page=2">', false);

        $this->get(route('noticias.index', ['page' => 1]))
            ->assertSee('<link rel="canonical" href="'.route('noticias.index').'">', false);
    }

    /**
     * Valida que todas as páginas publicam a organização como Organization + ReligiousOrganization (AC 8.3).
     */
    public function test_layout_renders_religious_organization_structured_data(): void
    {
        $this->withoutVite();

        foreach (['home', 'quem-somos', 'contato'] as $route) {
            $data = $this->structuredData($this->get(route($route))->getContent());
            $organization = collect($data)->first(fn (array $item) => ($item['@type'] ?? null) === ['Organization', 'ReligiousOrganization']);

            $this->assertNotNull($organization, "JSON-LD da organização ausente em {$route}");
            $this->assertSame('Convenção Batista Nacional do Estado de Goiás', $organization['name']);
            $this->assertSame(url('/'), $organization['url']);
            $this->assertSame(config('cbngo.contact.email'), $organization['email']);
            $this->assertContains(config('cbngo.social.instagram'), $organization['sameAs']);
        }
    }

    /**
     * Valida o JSON-LD NewsArticle e o BreadcrumbList da notícia (AC 8.3).
     */
    public function test_news_page_renders_news_article_structured_data(): void
    {
        $this->withoutVite();

        $news = News::factory()->create([
            'title' => 'Assembleia Estadual 2026',
            'slug' => 'assembleia-estadual-2026',
            'featured_image' => 'news/featured/assembleia.jpg',
        ]);

        $data = collect($this->structuredData($this->get(route('noticias.show', $news->slug))->getContent()));

        $article = $data->firstWhere('@type', 'NewsArticle');
        $this->assertNotNull($article);
        $this->assertSame('Assembleia Estadual 2026', $article['headline']);
        $this->assertSame(['https://midia.test/news/featured/assembleia.jpg'], $article['image']);
        $this->assertSame($news->published_at->toIso8601String(), $article['datePublished']);
        $this->assertSame(route('noticias.show', $news->slug), $article['mainEntityOfPage']);
        $this->assertSame('Convenção Batista Nacional do Estado de Goiás', $article['publisher']['name']);

        $breadcrumbs = $data->firstWhere('@type', 'BreadcrumbList');
        $this->assertNotNull($breadcrumbs);
        $this->assertCount(3, $breadcrumbs['itemListElement']);
    }

    /**
     * Valida o JSON-LD Article com autor Person na página de artigo (AC 8.3).
     */
    public function test_article_page_renders_article_structured_data_with_author(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor(['name' => 'Pr. Roberto Silveira', 'slug' => 'pr-roberto-silveira']);
        $article = Article::factory()->create(['user_id' => $author->user_id, 'slug' => 'a-graca-que-transforma']);

        $data = collect($this->structuredData($this->get(route('artigos.show', $article->slug))->getContent()));

        $schema = $data->firstWhere('@type', 'Article');
        $this->assertNotNull($schema);
        $this->assertSame('Person', $schema['author']['@type']);
        $this->assertSame('Pr. Roberto Silveira', $schema['author']['name']);
        $this->assertSame(route('autores.show', 'pr-roberto-silveira'), $schema['author']['url']);
    }

    /**
     * Valida que conteúdo com </script> não fecha a tag do JSON-LD (Edge Case 2).
     */
    public function test_structured_data_escapes_script_tags(): void
    {
        $this->withoutVite();

        $news = News::factory()->create([
            'title' => 'Título </script><script>alert(1)</script>',
            'slug' => 'titulo-malicioso',
        ]);

        $response = $this->get(route('noticias.show', $news->slug));

        $response->assertStatus(200);
        $response->assertDontSee('</script><script>alert(1)', false);

        $article = collect($this->structuredData($response->getContent()))->firstWhere('@type', 'NewsArticle');
        $this->assertSame('Título </script><script>alert(1)</script>', $article['headline']);
    }

    /**
     * Valida que o sitemap lista rotas estáticas e somente conteúdos publicados (AC 8.2, Edge Cases 5 e 6).
     */
    public function test_sitemap_lists_static_routes_and_published_content_only(): void
    {
        $published = News::factory()->create(['slug' => 'noticia-publicada']);
        $draft = News::factory()->create(['slug' => 'noticia-rascunho', 'status' => 'draft']);
        $scheduled = News::factory()->create(['slug' => 'noticia-agendada', 'published_at' => now()->addWeek()]);
        $pending = News::factory()->pendingReview()->create(['slug' => 'noticia-pendente']);
        $archived = News::factory()->archived()->create(['slug' => 'noticia-arquivada']);

        $author = $this->createAuthor(['slug' => 'autor-ativo']);
        $silentAuthor = $this->createAuthor(['slug' => 'autor-sem-artigos']);
        Article::factory()->create(['user_id' => $author->user_id, 'slug' => 'artigo-publicado']);
        Article::factory()->create(['user_id' => $silentAuthor->user_id, 'slug' => 'artigo-rascunho', 'status' => 'draft']);
        $inactiveAuthor = $this->createAuthor(['slug' => 'autor-inativo', 'is_active' => false]);
        Article::factory()->byAuthor($inactiveAuthor)->create(['slug' => 'artigo-de-autor-inativo']);

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $this->assertStringStartsWith('application/xml', (string) $response->headers->get('Content-Type'));

        $xml = simplexml_load_string((string) $response->getContent());
        $this->assertNotFalse($xml);
        $locations = array_map(fn ($url) => (string) $url->loc, iterator_to_array($xml->url, false));

        foreach (['home', 'quem-somos', 'diretoria', 'contato', 'igrejas', 'noticias.index', 'artigos.index'] as $route) {
            $this->assertContains(route($route), $locations);
        }

        $this->assertContains(route('noticias.show', $published->slug), $locations);
        $this->assertContains(route('artigos.show', 'artigo-publicado'), $locations);
        $this->assertContains(route('autores.show', 'autor-ativo'), $locations);

        $this->assertNotContains(route('noticias.show', $draft->slug), $locations);
        $this->assertNotContains(route('noticias.show', $scheduled->slug), $locations);
        $this->assertNotContains(route('noticias.show', $pending->slug), $locations);
        $this->assertNotContains(route('noticias.show', $archived->slug), $locations);
        $this->assertNotContains(route('autores.show', 'autor-inativo'), $locations);
        $this->assertNotContains(route('artigos.show', 'artigo-rascunho'), $locations);
        $this->assertNotContains(route('autores.show', 'autor-sem-artigos'), $locations);
        $this->assertNotContains(route('design-system'), $locations);

        $response->assertSee('<lastmod>'.$published->updated_at->toAtomString().'</lastmod>', false);
    }

    /**
     * Valida que o sitemap usa o TTL configurado (10 min por padrão), para que o conteúdo do painel apareça no prazo.
     */
    public function test_sitemap_cache_uses_configured_ttl(): void
    {
        $this->assertSame(600, config('cbngo.sitemap.cache_ttl'));

        config(['cbngo.sitemap.cache_ttl' => 120]);

        Cache::shouldReceive('remember')
            ->once()
            ->with('sitemap.entries', 120, \Mockery::type(\Closure::class))
            ->andReturnUsing(fn (string $key, int $ttl, \Closure $callback) => $callback());

        $this->get('/sitemap.xml')->assertStatus(200);
    }

    /**
     * Valida que conteúdo publicado depois do sitemap em cache aparece quando o cache expira.
     */
    public function test_sitemap_reflects_new_content_after_cache_expires(): void
    {
        $this->get('/sitemap.xml')->assertStatus(200);

        $news = News::factory()->create(['slug' => 'publicada-no-painel']);
        $this->get('/sitemap.xml')->assertDontSee(route('noticias.show', $news->slug), false);

        $this->travel(config('cbngo.sitemap.cache_ttl') + 1)->seconds();
        $this->get('/sitemap.xml')->assertSee(route('noticias.show', $news->slug), false);
    }

    /**
     * Valida o robots.txt com sitemap absoluto e bloqueio das rotas internas (AC 8.2).
     */
    public function test_robots_txt_points_to_sitemap_and_blocks_internal_routes(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $this->assertStringStartsWith('text/plain', (string) $response->headers->get('Content-Type'));
        $response->assertSee('User-agent: *', false);
        $response->assertSee('Disallow: /design-system', false);
        $response->assertSee('Disallow: /health', false);
        $response->assertSee('Sitemap: '.url('/sitemap.xml'), false);
        $this->assertFileDoesNotExist(public_path('robots.txt'));
    }

    /**
     * Valida que o sitemap inclui image:image e image:loc quando há imagem e omite quando não há.
     */
    public function test_sitemap_includes_image_loc_when_media_is_present(): void
    {
        config(['media.url' => 'https://midia.cbngo.org.br']);

        $newsWithImage = News::factory()->create([
            'slug' => 'noticia-com-foto',
            'featured_image' => 'news/featured/foto1.jpg',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $newsWithoutImage = News::factory()->create([
            'slug' => 'noticia-sem-foto',
            'featured_image' => null,
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $author = $this->createAuthor(['avatar' => 'authors/avatars/autor.jpg', 'slug' => 'autor-com-avatar']);
        $article = Article::factory()->byAuthor($author)->create([
            'slug' => 'artigo-com-capa',
            'cover_image' => 'articles/covers/capa.jpg',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertSee('xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"', false);
        $response->assertSee('<image:loc>https://midia.cbngo.org.br/news/featured/foto1.jpg</image:loc>', false);
        $response->assertSee('<image:loc>https://midia.cbngo.org.br/articles/covers/capa.jpg</image:loc>', false);
        $response->assertSee('<image:loc>https://midia.cbngo.org.br/authors/avatars/autor.jpg</image:loc>', false);

        $content = $response->getContent();
        // A url da notícia sem foto não deve ter tag image:image no bloco da tag url
        $this->assertMatchesRegularExpression('#<url>\s*<loc>[^<]*noticia-sem-foto</loc>\s*<lastmod>[^<]*</lastmod>\s*</url>#s', $content);
    }

    /**
     * Valida que o design system interno não é indexável.
     */
    public function test_design_system_is_not_indexable(): void
    {
        $this->withoutVite();

        $this->get(route('design-system'))
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    /**
     * Valida metatags, og:image da capa e JSON-LD Project vinculado à igreja no detalhe do projeto social (P.6).
     */
    public function test_social_project_page_renders_meta_and_project_structured_data(): void
    {
        $this->withoutVite();

        $church = Church::factory()->create(['name' => 'Igreja Batista Nacional Luz', 'city' => 'Luziânia']);
        SocialProject::factory()->forChurch($church)->category(ProjectCategory::CULTURAL)->create([
            'title' => 'Orquestra Jovem Luz',
            'slug' => 'orquestra-jovem-luz',
            'summary' => 'Aulas de música para adolescentes da comunidade.',
            'cover_image' => 'projects/covers/orquestra.jpg',
            'started_at' => '2025-08-01',
        ]);

        $response = $this->get('/projetos-sociais/orquestra-jovem-luz');

        $response->assertSee('<title>Orquestra Jovem Luz — CBN Goiás</title>', false);
        $response->assertSee('<meta name="description" content="Aulas de música para adolescentes da comunidade.">', false);
        $response->assertSee('<meta property="og:image" content="https://midia.test/projects/covers/orquestra-cover.webp">', false);

        $project = collect($this->structuredData((string) $response->getContent()))->firstWhere('@type', 'Project');

        $this->assertNotNull($project);
        $this->assertSame('Orquestra Jovem Luz', $project['name']);
        $this->assertSame(route('projetos-sociais.show', 'orquestra-jovem-luz'), $project['url']);
        $this->assertSame('2025-08-01', $project['foundingDate']);
        $this->assertSame('Luziânia', $project['areaServed']['name']);
        $this->assertSame('Igreja Batista Nacional Luz', $project['parentOrganization']['name']);
        $this->assertSame(route('igrejas.show', $church->slug), $project['parentOrganization']['url']);
        $this->assertSame(url('/').'#organizacao', $project['parentOrganization']['memberOf']['@id']);
    }

    /**
     * Valida que o sitemap lista a página e os projetos publicados (com imagem) e omite rascunhos (P.6, P.8).
     */
    public function test_sitemap_lists_published_social_projects_only(): void
    {
        $published = SocialProject::factory()->create(['cover_image' => 'projects/covers/horta.jpg']);
        $draft = SocialProject::factory()->draft()->create();

        $response = $this->get('/sitemap.xml');

        $response->assertSee('<loc>'.route('projetos-sociais.index').'</loc>', false);
        $response->assertSee('<loc>'.route('projetos-sociais.show', $published->slug).'</loc>', false);
        $response->assertSee('<image:loc>https://midia.test/projects/covers/horta.jpg</image:loc>', false);
        $response->assertDontSee(route('projetos-sociais.show', $draft->slug), false);
    }

    /**
     * Valida o JSON-LD Legislation do estatuto, identificado pela URI FRBR Work do Akoma Ntoso (Issue #33).
     */
    public function test_statute_page_renders_legislation_structured_data(): void
    {
        $this->withoutVite();

        Statute::factory()->create();

        $html = (string) $this->get('/estatuto')->getContent();
        $legislation = collect($this->structuredData($html))->firstWhere('@type', 'Legislation');

        $this->assertNotNull($legislation);
        $this->assertSame('/akn/br/act/estatuto/cbngo/2025-10-18', $legislation['legislationIdentifier']);
        $this->assertSame('2025-10-18', $legislation['legislationDate']);
        $this->assertSame('pt-BR', $legislation['inLanguage']);
        $this->assertSame('https://schema.org/InForce', $legislation['legislationLegalForce']);
        $this->assertSame(route('estatuto.xml'), $legislation['encoding']['contentUrl']);
        $this->assertSame(url('/').'#organizacao', $legislation['legislationPassedBy']['@id']);
    }

    /**
     * Valida que /estatuto entra no sitemap só quando há versão publicada.
     */
    public function test_sitemap_lists_statute_only_when_published(): void
    {
        $this->get('/sitemap.xml')->assertDontSee('<loc>'.route('estatuto.show').'</loc>', false);

        Cache::forget('sitemap.entries');
        Statute::factory()->create();

        $this->get('/sitemap.xml')->assertSee('<loc>'.route('estatuto.show').'</loc>', false);
    }

    /**
     * A arte padrão de compartilhamento existe e tem 1200×630 (Issue #43, S.1).
     */
    public function test_default_share_image_is_1200_by_630(): void
    {
        $path = public_path((string) config('cbngo.seo.default_image'));

        $this->assertFileExists($path);
        $this->assertSame([1200, 630], array_slice((array) getimagesize($path), 0, 2));
    }

    /**
     * Artigo com capa usa a variante cover; sem capa, cai no avatar do articulista (Issue #43, S.1).
     */
    public function test_article_share_image_uses_cover_variant(): void
    {
        $this->withoutVite();

        $author = $this->createAuthor(['slug' => 'autor-capa']);
        $article = Article::factory()->byAuthor($author)->create(['slug' => 'artigo-capa', 'cover_image' => 'articles/covers/graca.jpg']);

        $this->get(route('artigos.show', $article->slug))
            ->assertSee('<meta property="og:image" content="https://midia.test/articles/covers/graca-cover.webp">', false);
    }

    /**
     * A home tem o JSON-LD WebSite com o nome do site; as outras páginas não (Issue #43, S.3).
     */
    public function test_home_renders_website_structured_data_only_on_home(): void
    {
        $this->withoutVite();

        $website = collect($this->structuredData((string) $this->get(route('home'))->getContent()))->firstWhere('@type', 'WebSite');

        $this->assertNotNull($website);
        $this->assertSame(url('/').'#site', $website['@id']);
        $this->assertSame('CBN Goiás', $website['name']);
        $this->assertSame(['CBN-GO', 'Convenção Batista Nacional do Estado de Goiás'], $website['alternateName']);
        $this->assertSame(url('/'), $website['url']);
        $this->assertSame('pt-BR', $website['inLanguage']);
        $this->assertSame(url('/').'#organizacao', $website['publisher']['@id']);
        $this->assertArrayNotHasKey('potentialAction', $website);

        $contact = collect($this->structuredData((string) $this->get(route('contato'))->getContent()))->firstWhere('@type', 'WebSite');
        $this->assertNull($contact);
    }

    /**
     * O sitemap lista as páginas das igrejas ativas, com logo, e omite as inativas (Issue #43, S.2).
     */
    public function test_sitemap_lists_active_church_pages_only(): void
    {
        $active = Church::factory()->create(['slug' => 'ibn-ativa', 'logo' => 'churches/logos/ativa.png']);
        Church::factory()->create(['slug' => 'ibn-inativa', 'is_active' => false]);

        $response = $this->get('/sitemap.xml');

        $response->assertSee('<loc>'.route('igrejas.show', 'ibn-ativa').'</loc>', false);
        $response->assertSee('<lastmod>'.$active->updated_at->toAtomString().'</lastmod>', false);
        $response->assertSee('<image:loc>https://midia.test/churches/logos/ativa.png</image:loc>', false);
        $response->assertDontSee('ibn-inativa', false);
    }

    /**
     * Extrai e decodifica os blocos JSON-LD do HTML renderizado.
     *
     * @return array<int, array<string, mixed>>
     */
    private function structuredData(string $html): array
    {
        preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $matches);

        return array_map(fn (string $json) => json_decode($json, true, 512, JSON_THROW_ON_ERROR), $matches[1]);
    }
}
