# Especificação Técnica (Spec) — Issue #43: Ajustes de SEO

- **Issue:** [#43 — [SEO] Ajustes de SEO: imagem de compartilhamento, páginas das igrejas e verificação em produção](https://github.com/cbn-go/portal-cbngo-site/issues/43)
- **Base:** [#8 — Auditoria de Performance, Acessibilidade e SEO](https://github.com/cbn-go/portal-cbngo-site/issues/8)
- **Fase WDLC:** Fase 4 (Garantia da Qualidade & SEO)
- **Metodologia:** Spec-Driven Development (SDD) com ciclo TDD Mandatório
- **Data:** 30 de Outubro de 2026
- **Status:** Aprovada

---

## 1. Objetivo & Escopo

Completar o SEO do portal em três frentes:
- a imagem de compartilhamento;
- uma página própria para cada igreja, para a busca local;
- o nome do site nos resultados do Google.

### 1.1 Goals
- **S.1 — Imagem de compartilhamento:**
  - arte padrão `images/brand/cbn-go-share.png` (1200×630) no lugar do logo (601×247);
  - `og:image:width` e `og:image:height` só para a arte padrão;
  - `og:image:alt` e `twitter:image:alt` com o título;
  - notícia, artigo e projeto usam a variante `cover` da capa (WebP, 1600px), com fallback para o original.
- **S.2 — Página da igreja `/igrejas/{slug}`:**
  - **Conteúdo:**
    - logo, nome (`h1`), cidade, pastor e endereço completo, com Google Maps e Waze;
    - horários de culto, telefones, e-mail e redes;
    - até 3 projetos sociais e até 3 notícias publicadas da igreja.
  - **SEO:**
    - title "{Nome} em {Cidade}";
    - description com cidade, endereço e cultos;
    - JSON-LD `Church` e `BreadcrumbList`.
  - **Navegação e sitemap:**
    - igreja inativa ou inexistente responde 404;
    - o card do diretório e o link da igreja no projeto social apontam para a página;
    - as igrejas ativas entram no `sitemap.xml`.
- **S.3 — JSON-LD `WebSite` na home,** com `name` "CBN Goiás" e `alternateName`.

### 1.2 Non-Goals e decisões
- **Descriptions das páginas institucionais (item 4 da issue):** já existem em todas as páginas.
- **Horários de culto:** não entram como `openingHoursSpecification`. O painel guarda só o início do culto, e o schema exige `opens` e `closes`. Os horários aparecem no texto e na description.
- **`SearchAction` no `WebSite`:** fica de fora, porque o site não tem rota de busca.
- **Checklist de produção (item 5 da issue):** é feito depois do deploy. Cobre Search Console, Bing, perfil do Google, testes de compartilhamento e de pesquisa aprimorada, e Lighthouse.
- **Conteúdo do painel:** a revisão de notícias com termos incorretos é feita no painel.

---

## 2. Contratos

### 2.1 `App\Support\Media::shareImage(?string $path): ?string`
Retorna a variante `cover` ou, sem ela, `Media::url($path)`. URL insegura retorna `null`, e a página cai na arte padrão.

### 2.2 `config/cbngo.php`
`seo.default_image` = `images/brand/cbn-go-share.png`, `seo.default_image_width` = 1200, `seo.default_image_height` = 630.

### 2.3 Rota e controller
`GET /igrejas/{slug}` → `ChurchController::show`, com o nome `igrejas.show`:
- busca com `Church::active()->where('slug', …)->firstOrFail()`;
- carrega `SocialProject::published()` e `News::published()` da igreja, até 3 de cada.

### 2.4 `App\Support\StructuredData`
- **`church(Church)`:**
  - `@type` `Church`, `name`, `url`, `image`;
  - `address` `PostalAddress`;
  - `geo` `GeoCoordinates`, só com coordenadas válidas;
  - `telephone`, `email`, `sameAs`;
  - `parentOrganization` → `#organizacao`.
- **`website()`:**
  - `@type` `WebSite`, `@id` `#site`;
  - `name`, `alternateName`, `url`, `inLanguage` pt-BR;
  - `publisher` → `#organizacao`.

### 2.5 `App\Models\SocialProject::church_directory_url`
Passa a apontar para `route('igrejas.show', $church->slug)`.

---

## 3. Testes
- **`tests/Feature/ChurchPageTest.php`:**
  - a página traz conteúdo, contatos e rotas;
  - igreja inativa ou inexistente dá 404;
  - title e description;
  - JSON-LD `Church`, com `geo` só quando as coordenadas são válidas;
  - aparecem só os projetos e notícias publicados da própria igreja;
  - e-mail e redes inválidos não aparecem.
- **`tests/Feature/ChurchDirectoryTest.php`:** o card aponta para a página da igreja.
- **`tests/Feature/SeoTest.php`:**
  - a arte padrão sai com dimensões e `alt`;
  - a capa usa `-cover.webp`;
  - o `WebSite` aparece só na home;
  - o sitemap traz as igrejas ativas.
- **`tests/Feature/SocialProjectTest.php`:** o link da igreja aponta para a página.
