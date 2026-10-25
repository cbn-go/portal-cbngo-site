# Especificação Técnica (Spec) — Issue #23: Hero da Home com Banners de Aviso cadastrados no Painel

- **Issue:** [#23 — [Integração] Hero da Home com Banners de Aviso cadastrados no Painel](https://github.com/cbn-go/portal-cbngo-site/issues/23)
- **Cadastro no painel:** [admin#24 — Cadastro de Banners do Hero da Página Inicial](https://github.com/cbn-go/portal-cbngo-admin/issues/24)
- **Fase WDLC:** Integração Painel ↔ Portal Público
- **Metodologia:** Spec-Driven Development (SDD) com ciclo TDD Mandatório
- **Data:** 25 de Outubro de 2026
- **Status:** Aprovada

---

## 1. Objetivo & Escopo

### 1.1 Contexto & Objetivo
O hero da home (`resources/views/home.blade.php`) é estático: imagem do Unsplash e textos fixos. A secretaria passa a cadastrar **banners do hero** no painel administrativo (tabela `banners` do banco compartilhado), com imagem única, título, subtítulo e link opcional (interno ou externo). O portal lê esses banners e os exibe no hero: **um banner** vira hero simples; **vários banners** viram carrossel. Sem banners vigentes, o hero institucional atual é mantido como fallback.

Os banners do hero são diferentes dos avisos urgentes (`notices` → `<x-urgent-banner>`), que continuam sendo a barra fina no topo do site.

### 1.2 Goals (Dentro do Escopo)
- **B.1 — Model `Banner`:** leitura da tabela `banners`, scope `visible()` e accessors de mídia, link e corte (`focal_point`).
- **B.2 — Controller com cache:** `HomeController` carrega os banners visíveis com cache curto (5 min) cuja chave muda quando algum banner é criado, editado ou removido (`max(updated_at)` + `count(*)`).
- **B.3 — Componente `<x-hero-banner>`:** hero simples (1 banner) ou carrossel Alpine.js (N banners) com autoplay pausável, setas, indicadores e swipe; imagem responsiva com `srcset`/`sizes`, `object-fit: cover` e `object-position` derivado do `focal_point`; texto em HTML sobre overlay escuro; botão apenas quando houver link válido.
- **B.4 — Fallback:** sem banners vigentes, o hero institucional atual (título, subtítulo, "Conheça a CBN-GO" e "Encontre uma Igreja").
- **B.5 — Acessibilidade:** `aria-roledescription="carousel"`/`"slide"`, controles rotulados, navegação por teclado (← →), autoplay pausado no foco e no hover, botão de pausar e respeito a `prefers-reduced-motion`.
- **B.6 — Performance:** só o primeiro banner com `fetchpriority="high"` (e `loading="eager"`); os demais com `loading="lazy"`; altura fixa da seção e `width`/`height` nas imagens (sem CLS).
- **B.7 — Testes:** 0, 1 e N banners; banner expirado/agendado/inativo não aparece; link externo com `target`/`rel` corretos.

### 1.3 Non-Goals (Fora do Escopo)
- Cadastro, upload, reordenação e geração das variantes WebP (responsabilidade do painel, admin#24 e admin#20).
- Migrations de produção: o portal não migra o banco compartilhado; a migration em `database/migrations/testing/` é apenas espelho para local/testes.
- Alterações na barra de avisos urgentes (`notices`).

---

## 2. Contratos & Modelagem

### 2.1 Tabela `banners` (espelho de `portal-cbngo-admin/database/migrations/2026_10_25_100000_create_banners_table.php`)

| Coluna | Tipo | Observação |
|--------|------|------------|
| `title` | string(255) | Obrigatório |
| `subtitle` | string(255) nullable | |
| `image` | string | Caminho relativo no storage de mídia (`banners/…`) |
| `focal_point` | string(10) | `center` (padrão), `left`, `right` |
| `alt_text` | string(255) | Obrigatório no painel |
| `link_url` | string(255) nullable | Interno (`/…` ou mesmo host) ou externo |
| `link_label` | string(60) nullable | Padrão "Saiba mais" |
| `opens_in_new_tab` | bool | Marcado por padrão no painel para links externos |
| `position` | unsigned int | Ordem de exibição |
| `starts_at` / `ends_at` | timestamp nullable | Vigência |
| `is_active` | bool | |

### 2.2 Model `App\Models\Banner`
```php
Banner::visible();          // is_active, starts_at <= now (ou nulo), ends_at >= now (ou nulo), orderBy position, id

$banner->image_url;         // Media::url($image)
$banner->image_srcset;      // Media::srcset($image) — variantes WebP thumb/card/cover
$banner->object_position;   // center → "center center", left → "left center", right → "right center"
$banner->link;              // http(s) ou caminho relativo "/…"; qualquer outro esquema → null (XSS)
$banner->link_text;         // link_label ou "Saiba mais"
$banner->is_external;       // URL absoluta com host diferente de config('app.url')
$banner->opens_new_tab;     // opens_in_new_tab || is_external
```

### 2.3 Componente `<x-hero-banner :banners="$banners" />`
- Seção com altura fixa (`h-[560px] lg:h-[640px]`); slides empilhados (`absolute inset-0`).
- Imagem via `<x-media-image variant="cover" sizes="100vw">` com `width="1600" height="667"` e `object-position` inline.
- Título: `<h1>` no primeiro slide, `<h2>` nos demais (um único `h1` por página).
- Link externo: `target="_blank" rel="noopener noreferrer"`, ícone de link externo e texto acessível "(abre em nova aba)".
- N > 1: `x-data="heroCarousel({ count, interval })"` (registrado em `resources/js/hero-carousel.js`), setas, indicadores com `aria-current`, botão pausar/retomar; slides inativos com `aria-hidden` e `inert`. Sem JavaScript, apenas o primeiro slide é visível.

---

## 3. Casos de Borda & Falhas (Edge Cases)

1. **Banner com vigência iniciando/terminando sem edição:** a chave de cache não muda; o TTL de 5 minutos limita o atraso a no máximo 5 min.
2. **Banner removido no painel:** `count(*)` muda e o cache é renovado na próxima requisição.
3. **`link_url` com `javascript:`, `data:` ou `//host`:** descartado; o banner é exibido sem botão.
4. **Imagem ausente ou caminho inválido:** `x-media-image` exibe o placeholder institucional, sem imagem quebrada.
5. **Variantes WebP ainda em processamento:** `src` aponta para o original; `srcset` aponta para as variantes.
6. **`focal_point` desconhecido:** usa `center center`.
7. **Usuário com `prefers-reduced-motion`:** sem autoplay e sem transição de fade; navegação manual continua disponível.

---

## 4. Critérios de Aceitação & Plano de Verificação (TDD)

### Testes Unitários (`tests/Unit/BannerTest.php`)
- Scope `visible()` filtra inativos, agendados e expirados e ordena por `position`.
- Accessors `link`, `link_text`, `is_external`, `opens_new_tab` e `object_position`.

### Testes de Feature (`tests/Feature/HomeBannerTest.php`)
- 0 banners: hero institucional.
- 1 banner: título, subtítulo, alt e `srcset` WebP; sem controles de carrossel; sem hero institucional.
- N banners: ordem por `position`, controles do carrossel, `fetchpriority="high"` só no primeiro e `loading="lazy"` nos demais.
- Banner expirado, agendado ou inativo não aparece.
- Link externo com `target="_blank"` e `rel="noopener noreferrer"`; link interno sem `target`; sem link, sem botão; `javascript:` descartado.
- `focal_point=left` → `object-position: left center`.
- Edição de banner reflete na requisição seguinte (cache invalidado por `updated_at`).

### Definition of Done
O hero exibe os banners do painel na ordem e no período definidos, com links funcionando, Lighthouse sem regressão de LCP/CLS e fallback institucional quando não houver banners.
