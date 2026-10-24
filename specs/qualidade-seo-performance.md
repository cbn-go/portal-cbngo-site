# Especificação Técnica (Spec) — Issue #8: Auditoria de Performance, Acessibilidade e SEO

- **Issue:** [#8 — [WDLC-4: QA] Auditoria de Performance, Acessibilidade e SEO](https://github.com/cbn-go/portal-cbngo-site/issues/8)
- **Fase WDLC:** Fase 4 (Garantia da Qualidade & SEO)
- **Metodologia:** Spec-Driven Development (SDD) com ciclo TDD Mandatório
- **Data:** 24 de Outubro de 2026
- **Status:** Implementado e Validado (Green TDD)

---

## 1. Objetivo & Escopo

### 1.1 Contexto & Objetivo
O portal público já tem todos os módulos de conteúdo (notícias, artigos, igrejas, institucional), mas o `<head>` só emite `<title>`. Não há metatags de descrição, OpenGraph, Twitter Cards, dados estruturados, `sitemap.xml` nem `robots.txt`. O objetivo é tornar o portal descobrível e compartilhável e atingir as metas do Google Lighthouse.

Também corrige a duplicação de marca no título: hoje o layout prefixa `CBN Goiás - ` e as views já terminam com ` — CBN Goiás`.

### 1.2 Goals (Dentro do Escopo)
- **Metatags dinâmicas e OpenGraph (Sub-issue 8.1):**
  - Componente `<x-seo>` renderizado pelo layout. Ele recebe das views, via `@section`: `title`, `meta_description`, `og_type`, `og_image` e `robots`.
  - O título segue o formato `{título} — CBN Goiás`. Sem título, usa `CBN Goiás — Convenção Batista Nacional do Estado de Goiás`.
  - O componente emite:
    - `<meta name="description">` e `<link rel="canonical">`
    - `og:title`, `og:description`, `og:type`, `og:url`, `og:image` (URL absoluta), `og:site_name`, `og:locale`
    - `twitter:card=summary_large_image`, `twitter:title`, `twitter:description`, `twitter:image`
  - Notícias e artigos usam `og:type=article`, `excerpt` como descrição e a imagem de capa (notícia) ou o avatar do autor (artigo). Quando não há imagem, cai na imagem padrão de `config('cbngo.seo.default_image')`.
  - Artigos e notícias emitem `article:published_time`; artigos também emitem `article:author`.
  - `/design-system` usa `noindex, nofollow`.
- **Sitemap e robots (Sub-issue 8.2):**
  - `GET /sitemap.xml`: urlset com as rotas públicas estáticas, as notícias e artigos publicados (`lastmod`) e os perfis de autores que têm artigos publicados. Cache de 1 hora.
  - `GET /robots.txt`: libera o site, bloqueia `/design-system` e `/health` e aponta para o sitemap com URL absoluta.
- **Dados estruturados Schema.org (Sub-issue 8.3):**
  - Todas as páginas: `Organization` + `ReligiousOrganization`, com nome, URL, logo, contato, endereço e `sameAs` com as redes oficiais.
  - `/noticias/{slug}`: `NewsArticle`. `/artigos/{slug}`: `Article`, com `author` do tipo `Person`. Ambos com `BreadcrumbList`.
- **Performance e acessibilidade (Sub-issue 8.4):**
  - Leaflet sai do bundle principal: o mapa de Goiás passa a ser carregado por `import()` dinâmico, só na página que o contém.
  - Imagens com dimensões explícitas e `loading="lazy"` abaixo da dobra; a imagem principal da notícia ganha `fetchpriority="high"`.
  - Remoção do Google Fonts (Plus Jakarta Sans / Playfair Display), que bloqueava a renderização e só servia de fallback das fontes locais da marca. As fontes locais passam a ser pré-carregadas.
  - Imagem do hero da home como `<img>` responsivo (`srcset` 640/1280/1920 e `fetchpriority="high"`).
  - Correções guiadas pelo relatório do Lighthouse:
    - hierarquia de títulos do rodapé (`h4` → `h2`);
    - `<label class="sr-only">` nos filtros de notícias e artigos;
    - contraste de cores (seção 2.5);
    - favicon.
- **Conformidade com o Manual da Marca:** logo colorido "CBN | GO" (versão preferencial, seção 5.1.1) e favicon/apple-touch-icon com o isotipo, ambos extraídos do arquivo oficial `CBN_ESTADUAIS.ai`.

### 1.3 Non-Goals (Fora do Escopo)
- Arte própria de compartilhamento (1200×630). Por ora, a imagem padrão é o logo oficial colorido.
- Validação do tom `cbn-orange-ink` pela CBN Comunicação: ele não consta do Manual da Marca e deve ser oficializado por eles.
- Lighthouse CI no GitHub Actions. As notas são medidas localmente e registradas na PR.
- Configuração de compressão e cache HTTP do servidor de produção (infraestrutura).

---

## 2. Contratos & Modelagem

### 2.1 Configuração (`config/cbngo.php`)
```php
'organization' => [
    'name' => 'Convenção Batista Nacional do Estado de Goiás',
    'short_name' => 'CBN Goiás',
    'founding_date' => '1967',
],

'seo' => [
    'default_description' => '…',
    'default_image' => 'images/brand/cbn-go.png',
],
```

### 2.2 Seções de SEO das Views
| Seção | Padrão | Exemplo |
|---|---|---|
| `title` | vazio (título da marca) | `@section('title', $news->title)` |
| `meta_description` | `cbngo.seo.default_description` | `@section('meta_description', $news->excerpt)` |
| `og_type` | `website` | `@section('og_type', 'article')` |
| `og_image` | `cbngo.seo.default_image` | `@section('og_image', $news->image_url)` |
| `robots` | `index, follow` | `@section('robots', 'noindex, nofollow')` |
| `published_time` | — | `@section('published_time', $news->published_at->toIso8601String())` |
| `author_name` | — | `@section('author_name', $article->author->name)` |

A descrição passa por `strip_tags`, tem o espaço em branco normalizado e é limitada a 160 caracteres. `og:image` só aceita URL `http(s)` ou caminho local; qualquer outro esquema cai na imagem padrão. O canonical é a URL atual sem query string, exceto `?page=N` quando `N > 1`.

### 2.3 Dados Estruturados (`App\Support\StructuredData`)
- `organization(): array<string, mixed>`
- `newsArticle(News $news): array<string, mixed>`
- `article(Article $article): array<string, mixed>`
- `breadcrumbs(array<int, array{name: string, url: string}> $items): array<string, mixed>`
- `toJson(array $data): string`: `json_encode` com `JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP`. Isso impede que um `</script>` vindo do banco feche a tag.

As views publicam os blocos com `@push('structured-data')`, e o layout os renderiza no `<head>` com `@stack('structured-data')`.

### 2.4 Rotas & Controller
```php
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');
```
- `index()`: view `sitemap` com `Content-Type: application/xml; charset=UTF-8`. As entradas ficam em cache de 1 hora (`Cache::remember('sitemap.entries', 3600, …)`).
- `robots()`: resposta `text/plain; charset=UTF-8`. Não existe `public/robots.txt`, para que a rota não seja sombreada pelo servidor web.

### 2.5 Tokens de Cor Acessíveis (WCAG 2.1 AA)
O laranja institucional `#F43517` tem contraste de 3,91:1 sobre branco, abaixo do mínimo de 4,5:1 para texto pequeno. Por decisão de produto, a cor da marca não muda; um tom derivado é usado apenas em textos pequenos e botões:

| Token | Hex | Contraste | Uso |
|---|---|---|---|
| `cbn-orange` | `#F43517` | 3,91:1 (branco) / 5,06:1 (preto) | Ícones, bordas, anéis de foco, detalhes decorativos e texto sobre fundos escuros |
| `cbn-orange-ink` | `#D42D12` | 5,02:1 (branco) | Textos pequenos, links e botões com texto branco |
| `cbn-orange-ink-dark` | `#B02510` | 6,73:1 (branco) | Hover de botões e texto sobre fundos laranja-claros |

Textos secundários em cinza sobre fundo claro usam `neutral-500` (4,7:1) em vez de `neutral-400` (2,5:1).

---

## 3. Casos de Borda & Falhas (Edge Cases)

1. **Notícia sem `image_url`:** `og:image` e `twitter:image` usam a imagem padrão absoluta; o JSON-LD usa o logo.
2. **Título ou resumo com `</script>` ou aspas:** as metatags saem escapadas pelo Blade e o JSON-LD sai com `<`.
3. **Resumo com HTML ou acima de 160 caracteres:** as tags são removidas e o texto é truncado.
4. **`image_url` com esquema inseguro (`javascript:`):** cai na imagem padrão.
5. **Notícia/artigo em rascunho ou agendado:** fora do sitemap.
6. **Autor sem artigos publicados:** fora do sitemap.
7. **Listagens paginadas:** o canonical mantém apenas `?page=N` (N > 1) e descarta filtros.

---

## 4. Critérios de Aceitação & Plano de Verificação

### 4.1 Critérios de Aceitação (Checklist da Issue #8)
- [x] **8.1:** `<title>`, `<meta description>`, `og:image`, `og:title` e Twitter Cards dinâmicos em notícias e artigos.
- [x] **8.2:** `sitemap.xml` dinâmico e `robots.txt`.
- [x] **8.3:** Schema.org `NewsArticle`, `Organization` e `ReligiousOrganization`.
- [x] **8.4:** Lighthouse com Performance ≥ 90, Acessibilidade ≥ 95, Melhores Práticas ≥ 95 e SEO = 100, em mobile e desktop (notas na PR).

### 4.2 Plano de Verificação Automatizada (Testes TDD)
Suíte `tests/Feature/SeoTest.php`:
1. `test_home_renders_brand_title_default_description_and_open_graph()`
2. `test_news_page_renders_dynamic_meta_and_open_graph_tags()`
3. `test_news_without_image_falls_back_to_default_share_image()`
4. `test_meta_description_is_stripped_and_limited()`
5. `test_article_page_renders_article_meta_and_author()`
6. `test_canonical_ignores_filters_but_keeps_page()`
7. `test_layout_renders_religious_organization_structured_data()`
8. `test_news_page_renders_news_article_structured_data()`
9. `test_article_page_renders_article_structured_data_with_author()`
10. `test_structured_data_escapes_script_tags()`
11. `test_sitemap_lists_static_routes_and_published_content_only()`
12. `test_robots_txt_points_to_sitemap_and_blocks_internal_routes()`
13. `test_design_system_is_not_indexable()`

### 4.3 Resultado do Lighthouse (13.5, 24/10/2026)
Medido com `php artisan serve` atrás de um proxy local com gzip e cache de assets (emulando o servidor de produção), banco com o `DatabaseSeeder`.

| Página | Mobile (Perf / A11y / BP / SEO) | Desktop (Perf / A11y / BP / SEO) |
|---|---|---|
| `/` | 97 / 100 / 100 / 100 | 100 / 100 / 100 / 100 |
| `/quem-somos` | 99 / 100 / 100 / 100 | 100 / 100 / 100 / 100 |
| `/contato` | 99 / 100 / 100 / 100 | 100 / 100 / 100 / 100 |
| `/igrejas` | 97 / 100 / 100 / 100 | 100 / 100 / 100 / 100 |
| `/noticias` | 99 / 100 / 100 / 100 | 100 / 100 / 100 / 100 |
| `/noticias/{slug}` | 99 / 100 / 100 / 100 | 100 / 100 / 100 / 100 |
| `/artigos/{slug}` | 99 / 100 / 100 / 100 | 100 / 100 / 100 / 100 |

Sem compressão (o `artisan serve` puro), a Performance mobile cai para 76–95. O servidor de produção precisa servir gzip/brotli e `Cache-Control` longo para `/build/*`.
