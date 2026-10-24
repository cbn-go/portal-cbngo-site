# Especificação Técnica (Spec) — Issue #19: Consumir o banco do Painel Administrativo

- **Issue:** [#19 — [Integração] Consumir o banco do Painel Administrativo no Portal Público](https://github.com/cbn-go/portal-cbngo-site/issues/19)
- **Fase WDLC:** Integração Painel ↔ Portal Público
- **Metodologia:** Spec-Driven Development (SDD) com ciclo TDD Mandatório
- **Data:** 24 de Outubro de 2026
- **Status:** Implementado e Validado (Green TDD)
- **Dependências:** [cbn-go/portal-cbngo-admin#18](https://github.com/cbn-go/portal-cbngo-admin/issues/18) (schema) e [cbn-go/portal-cbngo-admin#19](https://github.com/cbn-go/portal-cbngo-admin/issues/19) (diretoria e documentos)

---

## 1. Objetivo & Escopo

### 1.1 Contexto & Objetivo
Até aqui, o portal lia de um SQLite próprio, com schema próprio, preenchido por seeders. Nada do que era cadastrado no painel aparecia no site.

A partir desta issue, os dois sistemas usam **um único banco MySQL**:
- **O painel é o dono do schema.** Só ele executa migrations.
- **O portal só lê**, conectado com um usuário que tem apenas `SELECT`.

O mapeamento oficial de campos está em `portal-cbngo-admin/docs/integracao-portal-publico.md`. Esta spec registra como o portal o aplica.

### 1.2 Goals (Dentro do Escopo)
- **I.1 — Conexão:**
  - `.env.example` com `DB_CONNECTION=mysql` e o usuário `cbngo_site` (somente `SELECT`).
  - Sessão, cache e fila fora do banco: `file` / `file` / `sync`.
  - `MEDIA_URL` para montar as URLs de mídia.
- **I.2 — Migrations:**
  - O site deixa de ter migrations em `database/migrations/` e o deploy não executa `migrate`.
  - O schema do painel é espelhado em `database/migrations/testing/`, que só é carregado nos ambientes `local` e `testing`.
- **I.3 a I.7 — Models:** passam a refletir as tabelas do painel (seção 2).
- **I.8 — Sitemap:** o cache cai de 1 hora para 10 minutos, configurável.
- **I.9 — Testes:** factories e testes de feature passam a usar o novo schema.
- **Mínimo de mídia:** um helper único, `App\Support\Media::url()`, monta `MEDIA_URL/{caminho}`.
- **HTML de conteúdo:** o `content` de notícias e artigos (HTML do RichEditor) passa a ser renderizado, depois de sanitizado no próprio portal.

### 1.3 Non-Goals (Fora do Escopo — Issue #20)
- Versões WebP (`thumb`, `card`, `cover`) em `srcset`.
- Placeholders do Design System para mídia ausente.
- URLs absolutas de mídia em `og:image`, Schema.org e sitemap. Hoje `Seo::image()` já aceita a URL absoluta que `Media::url()` devolve.

---

## 2. Contratos & Modelagem

### 2.1 Regra de publicação (`news` e `articles`)
```
status = 'published' AND published_at IS NOT NULL AND published_at <= NOW()
```
`draft`, `pending_review` e `archived` nunca são exibidos: não aparecem em listagem, detalhe, home, filtros nem sitemap.

### 2.2 Mapeamento por model

| Model (tabela) | Antes | Agora |
|---|---|---|
| `News` (`news`) | `image_url` | `featured_image` → accessor `featured_image_url` |
| | `gallery_images` | `gallery` → accessor `gallery_urls` |
| | `church_name` / `city` | relação `church()`. Nula = "CBN Goiás" (`publisherName()` / `publisherCity()`) |
| | `is_published` | `status` + `published_at` |
| `Article` (`articles`) | `author_id` → `authors.id` | `user_id` → `author_profiles.user_id` (`author()`) |
| | — | `cover_image` → accessor `cover_image_url` |
| `Author` (`author_profiles` + `users`) | `name` | accessor a partir de `users.name` |
| | `role_title` | accessor `pastoral_title`, ou "Articulista CBN-GO" quando vazio |
| | `avatar_url` | accessor a partir de `avatar` |
| | — | escopo `active()`: só perfis de usuários com `users.is_active = 1` |
| `Church` (`churches`) | `logo_url` | `logo` → accessor `logo_url` |
| | `address` | `getFullAddress()` monta `address, number – complement` |
| `UrgentNotice` (`notices`) | `message` | accessor: `content` sem HTML (o painel não sanitiza avisos) |
| | `link` / `link_text` | `action_url` (só `http(s)` ou caminho relativo) / `action_label`, ou "Saiba mais" quando vazio |
| | `type` | `priority`: `normal → info`, `high → warning`, `urgent → critical` |
| | `ends_at` | `expires_at` |
| `BoardMember` (`board_members`) | `photo_url` | `photo`, lido via `Media::url()` |
| `Document` (`documents`) | `file_url` | `MEDIA_URL/{file}`; sem arquivo, usa `external_url` |

### 2.3 Avisos vigentes
Um aviso está vigente quando:
- `is_active = 1`;
- `starts_at` é nulo ou `<= NOW()`;
- `expires_at` é nulo ou `>= NOW()`.

Com mais de um vigente, a home exibe o de maior prioridade (`urgent` > `high` > `normal`). No empate, exibe o mais recente.

### 2.4 Filtros de notícias
- `?cidade=` filtra pela cidade da igreja emissora.
- `?igreja=` recebe o **slug** da igreja.
- As opções dos dois filtros vêm das igrejas ativas que têm notícias publicadas.

### 2.5 Mídia
`Media::url(?string $path)` devolve:
- `null` para vazio;
- a própria URL quando ela já é `http(s)://`;
- `rtrim(MEDIA_URL, '/').'/'.ltrim($path, '/')` nos demais casos.

### 2.6 Conteúdo HTML
`App\Support\Html::sanitize()` usa `symfony/html-sanitizer` e aplica:
- elementos seguros de texto (parágrafos, títulos, listas, citações, ênfase e tabelas);
- links apenas `http`/`https`/`mailto`;
- imagens apenas `http`/`https`;
- remoção de `<script>`, `<style>`, `<iframe>`, atributos `on*` e `style`.

O painel também sanitiza ao salvar. Esta é uma segunda camada de defesa.

### 2.7 Sitemap
- `Cache::remember('sitemap.entries', config('cbngo.sitemap.cache_ttl'))`, com padrão de 600 s (`SITEMAP_CACHE_TTL`).
- Não há invalidação por evento: o portal não observa as gravações do painel, e um `published_at` agendado vence sem que nada mude no banco. O TTL curto limita o atraso.

---

## 3. Plano de Testes (TDD)
- **Publicação:** `draft`, `pending_review`, `archived` e `published` com data nula ou futura ficam fora de listagem, detalhe, home e sitemap.
- **Notícias:**
  - Sem igreja, exibem "CBN Goiás".
  - Os filtros de cidade e igreja usam a relação.
  - A galeria e a capa usam `MEDIA_URL`.
- **Autores:**
  - Perfil por slug e por id.
  - Usuário inativo dá 404 e some dos filtros e do sitemap.
  - Fallback de `role_title`.
- **Igrejas:** endereço com número e complemento; inativas ficam ocultas.
- **Avisos:** mapeamento de prioridade para classe visual, desempate por prioridade, agendado e expirado ocultos, HTML do `content` não renderizado.
- **Institucional:**
  - Documento com `file` usa `MEDIA_URL`; sem `file`, usa `external_url`.
  - A foto da diretoria vem de `MEDIA_URL`.
- **Conteúdo:** HTML renderizado e sanitizado.
- **Sitemap:** respeita o TTL configurado.
- **Unit:** `Media::url()`.
