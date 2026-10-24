# Especificação Técnica (Spec) — Issue #8: Auditoria de Performance, Acessibilidade e SEO

- **Issue:** [#8 — [WDLC-4: QA] Auditoria de Performance, Acessibilidade e SEO](https://github.com/cbn-go/portal-cbngo-site/issues/8)
- **Fase WDLC:** Fase 4 (Garantia da Qualidade & SEO)
- **Metodologia:** Spec-Driven Development (SDD) com ciclo TDD Mandatório
- **Data:** 24 de Outubro de 2026
- **Status:** Em implementação

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
  - Correções guiadas pelo relatório do Lighthouse.

### 1.3 Non-Goals (Fora do Escopo)
- Arte própria de compartilhamento (1200×630). Por ora, a imagem padrão é o logo oficial.
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
- [ ] **8.1:** `<title>`, `<meta description>`, `og:image`, `og:title` e Twitter Cards dinâmicos em notícias e artigos.
- [ ] **8.2:** `sitemap.xml` dinâmico e `robots.txt`.
- [ ] **8.3:** Schema.org `NewsArticle`, `Organization` e `ReligiousOrganization`.
- [ ] **8.4:** Lighthouse com Performance ≥ 90, Acessibilidade ≥ 95, Melhores Práticas ≥ 95 e SEO = 100, em mobile e desktop (notas na PR).

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
