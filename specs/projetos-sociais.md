# Especificação Técnica (Spec) — Issue #27: Página de Projetos Sociais das Igrejas

- **Issue:** [#27 — [Integração] Página de Projetos Sociais das Igrejas](https://github.com/cbn-go/portal-cbngo-site/issues/27)
- **Cadastro no painel:** [admin#28 — Cadastro de Projetos Sociais das Igrejas (categorias Lausanne)](https://github.com/cbn-go/portal-cbngo-admin/issues/28)
- **Fase WDLC:** Integração Painel ↔ Portal Público
- **Metodologia:** Spec-Driven Development (SDD) com ciclo TDD Mandatório
- **Data:** 25 de Outubro de 2026
- **Status:** Aprovada

---

## 1. Objetivo & Escopo

### 1.1 Contexto & Objetivo
As igrejas da CBN-GO cadastram seus projetos sociais no painel administrativo (tabela `social_projects` do banco compartilhado). O representante da igreja envia o projeto para revisão e a convenção aprova a publicação. O portal público ganha a página **Projetos Sociais**, que dá visibilidade a essas ações e convida a comunidade a participar.

As categorias seguem a missão integral do **Pacto de Lausanne**: Social (art. 5), Cultural (art. 10), Educação e Liderança (art. 11).

### 1.2 Goals (Dentro do Escopo)
- **P.1 — Model `SocialProject`** (somente leitura): scope `published()`, relação `church()`, accessors de capa e galeria via `MEDIA_URL`.
- **P.2 — Listagem `/projetos-sociais`:** introdução com referência ao Pacto de Lausanne; filtros por categoria (chips), cidade, igreja e busca por texto na query string; cards com capa, badge de categoria, título, igreja, cidade, resumo e selo "Em andamento"; destaques primeiro, depois `published_at`; paginação de 9; estado vazio.
- **P.3 — Detalhe `/projetos-sociais/{slug}`:** capa, categoria, igreja (link para o diretório `/igrejas`), cidade, período, público atendido e beneficiados; descrição HTML sanitizada; galeria com lightbox (componente extraído da notícia); bloco "Como ajudar" com contato e botão de WhatsApp; botões de compartilhamento; "Outros projetos" da mesma categoria.
- **P.4 — Navegação:** item "Projetos Sociais" no menu principal e no rodapé.
- **P.5 — Home:** seção com até 3 projetos (destaques primeiro), exibida só quando houver projetos publicados.
- **P.6 — SEO:** metatags, `og:image` da capa, JSON-LD `Project` com a igreja como `Organization`, e inclusão no `sitemap.xml`.
- **P.7 — Acessibilidade e performance:** badges com contraste AA, `alt` nas imagens, lazy loading na listagem.
- **P.8 — Testes:** rascunho/pendente/arquivado/futuro nunca aparecem, filtros funcionam, slug inexistente → 404, projeto no sitemap.

### 1.3 Non-Goals (Fora do Escopo)
- Cadastro, revisão, upload e geração de variantes WebP (responsabilidade do painel).
- Página de detalhe de igreja (não existe; o link usa o diretório filtrado com âncora).
- Formulário de inscrição de voluntários ou doações online.

---

## 2. Contratos & Modelagem

### 2.1 Tabela `social_projects` (espelho de `portal-cbngo-admin/database/migrations/2026_10_26_110000_create_social_projects_table.php`)

| Coluna | Observação |
|--------|------------|
| `church_id` → `churches` | Obrigatório |
| `title`, `slug` | `slug` único; rota `/projetos-sociais/{slug}` |
| `category` | Valor = rótulo: `Social`, `Cultural`, `Educação`, `Liderança` |
| `summary` | Até 300 caracteres (cards) |
| `description` | HTML sanitizado pelo painel; o portal sanitiza de novo com `Html::sanitize` |
| `cover_image`, `gallery` (JSON) | Caminhos relativos no storage de mídia (`projects/covers`, `projects/galleries`) |
| `target_audience`, `beneficiaries_count`, `city` | `city` padrão = cidade da igreja |
| `started_at`, `ended_at` | `ended_at` nulo ou futuro = em andamento |
| `contact_name`, `contact_phone`, `contact_email`, `how_to_help` | Opcionais; `contact_phone` alimenta o botão de WhatsApp |
| `status`, `published_at`, `is_featured` | Público: `status = 'published'` e `published_at <= now()` |
| `review_notes` | Uso interno do painel; nunca exibido |

### 2.2 Enum `App\Enums\ProjectCategory`
Espelha o enum do painel. `slug()` (`social`, `cultural`, `educacao`, `lideranca`) e `fromSlug()` alimentam o filtro `?categoria=`; `description()` e `lausanneReference()` repetem os textos do painel; `badgeClasses()` define as cores dos badges com contraste AA.

### 2.3 Model `App\Models\SocialProject`
```php
SocialProject::published();   // status published + published_at <= now
SocialProject::ordered();     // is_featured desc, published_at desc, id desc

$project->church;             // somente igrejas ativas (como News)
$project->cover_image_url;    // Media::url
$project->gallery_urls;       // Media::urls
$project->is_ongoing;         // ended_at nulo ou >= hoje
$project->period_label;       // "Desde mar/2026" ou "mar/2026 – jun/2026"
$project->whatsapp_url;       // wa.me com texto pré-preenchido, ou null
$project->safe_contact_email; // e-mail válido ou null
$project->church_directory_url; // /igrejas?q={nome}#igreja-{slug}, ou null
```

### 2.4 Rotas
- `GET /projetos-sociais` → `projetos-sociais.index` (filtros `categoria`, `cidade`, `igreja`, `q`, `page`)
- `GET /projetos-sociais/{slug}` → `projetos-sociais.show`

### 2.5 Componentes
- `<x-project-card :project>`: card da listagem, da home e de "Outros projetos".
- `<x-gallery-lightbox :images>`: galeria com lightbox, extraída de `noticias/show.blade.php` (mesmo comportamento).
- `<x-share-buttons :title :url>`: WhatsApp, Facebook, X e Copiar link, extraídos de `noticias/show.blade.php`.

---

## 3. Casos de Borda & Falhas (Edge Cases)

1. **`categoria` inválida na URL:** o filtro é ignorado e todos os projetos são listados.
2. **Igreja inativa:** `church` é `null`; o card e o detalhe omitem a igreja e o link do diretório.
3. **Sem telefone ou telefone inválido:** sem botão de WhatsApp.
4. **E-mail inválido:** não é exibido.
5. **Sem capa:** placeholder do design system.
6. **Galeria vazia ou caminhos inseguros:** a seção de galeria não aparece (`Media::urls` descarta inválidos).
7. **HTML malicioso na descrição:** removido por `Html::sanitize`.
8. **Projeto encerrado:** continua visível, sem o selo "Em andamento", com o período completo.

---

## 4. Critérios de Aceitação & Plano de Verificação (TDD)

- `tests/Feature/SocialProjectTest.php`: listagem, visibilidade por status e data, ordem por destaque, paginação, filtros (categoria, inválida, cidade, igreja, busca), chips preservando filtros, estado vazio, detalhe completo, sanitização, WhatsApp condicional, outros projetos da mesma categoria, 404.
- `tests/Feature/SeoTest.php`: JSON-LD `Project`, `og:image` da capa, sitemap com projeto e `image:loc`, rascunho fora do sitemap.
- `tests/Feature/HomeTest.php`: seção só com projetos publicados, até 3, destaque primeiro; item no menu e no rodapé.
- `tests/Unit/ProjectCategoryTest.php`: `slug`/`fromSlug` e textos do Pacto de Lausanne.
- `tests/Feature/NewsTest.php` continua verde após a extração da galeria e do compartilhamento.

### Definition of Done
Um projeto publicado no painel aparece na listagem, no filtro da sua categoria e na página de detalhe, com contato para ajudar, em celular e desktop.
