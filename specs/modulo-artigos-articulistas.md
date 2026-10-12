# Especificação Técnica (Spec) — Issue #5: Módulo de Artigos e Articulistas

- **Issue:** [#5 — [WDLC-3: Public] Módulo de Artigos e Articulistas](https://github.com/cbn-go/portal-cbngo-site/issues/5)
- **Fase WDLC:** Fase 3 (Desenvolvimento do Portal Público)
- **Metodologia:** Spec-Driven Development (SDD) com ciclo TDD Mandatório
- **Data:** 12 de Outubro de 2026
- **Status:** Implementado e Validado (Green TDD)

---

## 1. Objetivo & Escopo

### 1.1 Contexto & Objetivo
Construir as páginas públicas dedicadas aos artigos de reflexão bíblica, teológica e ministerial da Convenção Batista Nacional do Estado de Goiás (CBN-GO), juntamente com os perfis biográficos públicos dos articulistas credenciados. O módulo deve oferecer excelência tipográfica editorial, alta legibilidade em qualquer dispositivo, filtros por autor e tags temáticas, além de ferramentas de compartilhamento.

### 1.2 Goals (Dentro do Escopo)
- **Feed Geral de Artigos (`/artigos`):**
  - Grid responsivo de artigos com paginação limpa (9 itens por página), preservando parâmetros de consulta via query string (`?page=2&q=...&autor=...&categoria=...`).
  - Campo de busca textual pesquisando por título, resumo e conteúdo.
  - Filtro dinâmico por articulista credenciado (`autor=slug`).
  - Filtro por tags/categorias temáticas (ex: Teologia, Liderança, Família, Missões).
  - Cards elegantes (`<x-article-card>`) com título, resumo em destaque, tempo estimado de leitura (`X min`), data de publicação, avatar e nome do autor linkado para seu perfil.
  - Tratamento de estado vazio (*empty state*) quando nenhum artigo corresponder aos filtros aplicados, com botão para redefinir a busca.
  - Destaque no navbar para o item "Artigos" com atributo `aria-current="page"`.
- **Página de Leitura do Artigo (`/artigos/{slug}`):**
  - Layout tipográfico editorial refinado focado em máximo conforto de leitura (`font-brand-serif` nos títulos e `font-brand-sans` no corpo de texto).
  - Metadados completos no topo: categoria temática, tempo estimado de leitura e data de publicação por extenso.
  - Conteúdo formatado com segurança total contra vulnerabilidades XSS (`{!! nl2br(e($article->content)) !!}`).
  - Botões de compartilhamento social para WhatsApp, Facebook, Twitter/X e Copiar Link com Clipboard API e fallback resiliente.
  - Bloco do Autor ao final do artigo contendo foto/avatar, titulação ministerial/acadêmica (`role_title`), biografia completa e links sociais.
  - Seção "Outros artigos recomendados" com os 3 artigos mais recentes publicados, excluindo o atual.
  - Link de retorno ao feed `/artigos`.
- **Página de Perfil do Autor (`/autores/{id_ou_slug}`):**
  - Rota flexível com suporte a resolução tanto por slug textual (`/autores/pr-roberto-silveira`) quanto por identificador numérico (`/autores/1`).
  - Apresentação biográfica completa com avatar em alta resolução, nome, titulação oficial da convenção, biografia detalhada e canais sociais.
  - Feed com a listagem de todos os artigos publicados pelo articulista com paginação.
  - Empty state gracioso caso o articulista ainda não possua publicações ativas.
- **Modelagem & Banco de Dados:**
  - Migração para adicionar coluna `category` (string, default 'Teologia') na tabela `articles`.
  - Migração para adicionar coluna `social_links` (json nullable) na tabela `authors`.
  - Casts e relacionamentos nos modelos `Article` e `Author`.
- **Qualidade & TDD:**
  - Suite de testes em `tests/Feature/ArticleTest.php` cobrindo 100% dos requisitos antes do código de produção.
  - Conformidade total com Pint e PHPStan nível 5.

### 1.3 Non-Goals (Fora do Escopo)
- Painel administrativo / CMS para submissão e aprovação de novos artigos (pertence ao escopo administrativo).
- Sistema de comentários públicos ou reações em artigos.
- Módulo de Notícias e Eventos (concluído na Issue #4).
- Diretório de Igrejas (Issue #6) ou Páginas Institucionais (Issue #7).

---

## 2. Contratos & Modelagem

### 2.1 Esquema de Dados (Database Schema)

**1. Migração `add_category_to_articles_table`:**
```php
Schema::table('articles', function (Blueprint $table) {
    $table->string('category', 100)->default('Teologia')->after('slug');
    $table->index(['category', 'is_published', 'published_at']);
});
```

**2. Migração `add_social_links_to_authors_table`:**
```php
Schema::table('authors', function (Blueprint $table) {
    $table->json('social_links')->nullable()->after('bio');
});
```

### 2.2 Estrutura das Entidades

**Tabela `articles`:**
| Campo | Tipo | Nulo | Descrição |
|---|---|---|---|
| `id` | `bigint unsigned` | Não | Chave primária |
| `author_id` | `bigint unsigned` | Não | Chave estrangeira para `authors` |
| `title` | `varchar(255)` | Não | Título do artigo |
| `slug` | `varchar(255)` | Não | Slug único para URL |
| `category` | `varchar(100)` | Não | Categoria temática (Teologia, Liderança, Família, Missões) |
| `excerpt` | `text` | Não | Resumo / epígrafe do artigo |
| `content` | `longtext` | Não | Conteúdo integral formatado |
| `reading_time` | `smallint unsigned`| Não | Tempo estimado de leitura em minutos |
| `is_published` | `tinyint(1)` | Não | Status de publicação |
| `published_at` | `timestamp` | Sim | Data e hora de publicação |
| `timestamps` | `timestamp` | Sim | Criação e atualização |

**Tabela `authors`:**
| Campo | Tipo | Nulo | Descrição |
|---|---|---|---|
| `id` | `bigint unsigned` | Não | Chave primária |
| `name` | `varchar(255)` | Não | Nome completo do articulista |
| `slug` | `varchar(255)` | Não | Slug único do autor |
| `role_title` | `varchar(150)` | Não | Titulação / cargo eclesiástico |
| `avatar_url` | `varchar(255)` | Sim | Foto de perfil do articulista |
| `bio` | `text` | Sim | Biografia ministerial e acadêmica |
| `social_links` | `json` | Sim | Links sociais (`{"instagram": "...", "facebook": "...", "email": "..."}`) |
| `timestamps` | `timestamp` | Sim | Auditoria temporal padrão |

### 2.3 Rotas & Assinaturas de Controllers

Em `routes/web.php`:
```php
Route::get('/artigos', [ArticleController::class, 'index'])->name('artigos.index');
Route::get('/artigos/{slug}', [ArticleController::class, 'show'])->name('artigos.show');
Route::get('/autores/{author}', [AuthorController::class, 'show'])->name('autores.show');
```

Assinaturas de Controller:
```php
namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View;
    public function show(string $slug): View;
}

class AuthorController extends Controller
{
    public function show(string $author): View;
}
```

### 2.4 Parâmetros de Filtro no Feed (`/artigos`)
| Parâmetro | Tipo | Exemplo | Finalidade |
|---|---|---|---|
| `q` | `string` | `"pastoreamento"` | Busca textual em título, resumo e conteúdo |
| `autor` | `string` | `"pr-roberto-silveira"` | Filtro por slug ou id do autor |
| `categoria` | `string` | `"Teologia"` | Filtro por tag temática |
| `page` | `int` | `2` | Número da página na paginação |

---

## 3. Casos de Borda & Falhas (Edge Cases)

1. **Artigo em rascunho ou com agendamento futuro:**
   - **Cenário:** Artigo com `is_published = false` ou `published_at > now()`.
   - **Comportamento:** Acesso a `/artigos/{slug}` retorna HTTP `404 Not Found`. O artigo não aparece no feed, nem na página do autor, nem nas recomendações.
2. **Slug inexistente de artigo:**
   - **Cenário:** Acesso a `/artigos/artigo-que-nao-existe`.
   - **Comportamento:** Retorna HTTP `404 Not Found`.
3. **Autor inexistente (por slug ou por ID numérico):**
   - **Cenário:** Acesso a `/autores/autor-inexistente` ou `/autores/999999`.
   - **Comportamento:** Retorna HTTP `404 Not Found`.
4. **Resolução híbrida de autor (slug vs ID):**
   - **Cenário:** Requisição para `/autores/1` ou `/autores/pr-roberto-silveira`.
   - **Comportamento:** Ambas resolvem com precisão o registro correto do autor sem conflitos.
5. **Autor sem foto (`avatar_url == null`):**
   - **Cenário:** Articulista cadastrado sem foto de avatar.
   - **Comportamento:** Exibe fallback com monograma / iniciais estilizadas em círculo contrastante de alta definição.
6. **Autor sem links sociais (`social_links == null` ou vazio):**
   - **Cenário:** Articulista sem dados sociais cadastrados.
   - **Comportamento:** Bloco de redes sociais é omitido elegantemente, sem erros de array ou ícones quebrados.
7. **Busca ou filtros sem resultados:**
   - **Cenário:** Nenhum artigo corresponde aos termos de busca ou categoria selecionada.
   - **Comportamento:** Retorna HTTP 200 com interface de empty state acolhedora e botão de limpeza de filtros.
8. **Proteção contra Cross-Site Scripting (XSS):**
   - **Cenário:** Conteúdo do artigo ou biografia contém tags maliciosas (`<script>`).
   - **Comportamento:** Sanitização obrigatória via `{!! nl2br(e(...)) !!}`.
9. **Compartilhamento social com caracteres especiais:**
   - **Cenário:** Título do artigo com aspas, acentuação e pontuação teológica.
   - **Comportamento:** Formatação com codificação RFC 3986 (`rawurlencode`), além de fallback via `document.execCommand` para copiar link.

---

## 4. Critérios de Aceitação & Plano de Verificação

### 4.1 Critérios de Aceitação (Checklist da Issue #5)

- [x] **Feed Geral de Artigos (`/artigos`):**
  - [x] Rota `/artigos` responde com HTTP 200 e layout oficial integrado.
  - [x] Paginação exibe até 9 artigos por página e preserva query strings nas URLs de paginação.
  - [x] Campo de busca textual filtra por título, resumo e conteúdo.
  - [x] Filtro por autor credenciado funcional.
  - [x] Filtro por categoria/tag temática (Teologia, Liderança, Família, Missões) funcional.
  - [x] Cards de artigo exibem autor, avatar/iniciais, tempo estimado de leitura e link para perfil do articulista.
  - [x] Empty state amigável exibido quando nenhum resultado for encontrado.
  - [x] Navbar destaca "Artigos" como item ativo com `aria-current="page"`.

- [x] **Leitura do Artigo (`/artigos/{slug}`):**
  - [x] Rota `/artigos/{slug}` responde com HTTP 200 para artigos publicados.
  - [x] Tipografia editorial legível com fontes da marca CBN (`Baker Signet` e `Humanist 521`).
  - [x] Exibe tempo de leitura, data formatada e categoria temática.
  - [x] Conteúdo sanitizado e formatado.
  - [x] Bloco do Autor completo ao final da matéria com foto, biografia e links sociais.
  - [x] Seção de artigos recomendados no rodapé (3 mais recentes, excluindo o atual).
  - [x] Botões sociais (WhatsApp, Facebook, Twitter/X, Copiar Link com fallback).
  - [x] Responde com HTTP 404 para artigos em rascunho, futuros ou slugs inexistentes.

- [x] **Perfil Público do Articulista (`/autores/{id_ou_slug}`):**
  - [x] Rota resolve com sucesso tanto por slug quanto por ID numérico.
  - [x] Exibe foto de alta qualidade, titulação e biografia completa do autor.
  - [x] Lista todos os artigos publicados pelo articulista com paginação.
  - [x] Oculta artigos em rascunho ou com agendamento futuro do perfil do articulista.
  - [x] Responde com HTTP 404 para autores inexistentes.

### 4.2 Plano de Verificação Automatizada (Testes TDD)

Será criado o arquivo `tests/Feature/ArticleTest.php` com 19 testes automatizados:

1. `test_articles_index_displays_published_articles_and_pagination()`: Valida listagem e paginação de 9 artigos.
2. `test_articles_index_hides_draft_and_future_articles()`: Garante barreira contra rascunhos e futuros.
3. `test_articles_index_can_search_by_title_and_content()`: Testa busca textual.
4. `test_articles_index_can_filter_by_author()`: Testa filtro por articulista.
5. `test_articles_index_can_filter_by_category_tag()`: Testa filtro por tag temática.
6. `test_articles_index_shows_empty_state_when_no_results_match_filters()`: Testa empty state e botão de limpeza.
7. `test_articles_pagination_preserves_query_string_parameters()`: Valida query string persistida entre páginas.
8. `test_articles_show_displays_article_content_and_reading_time()`: Valida exibição dos detalhes do artigo.
9. `test_articles_show_displays_author_block_with_bio_and_social_links()`: Valida bloco do autor e links sociais.
10. `test_articles_show_returns_404_for_unpublished_or_future_article()`: Testa barreira 404.
11. `test_articles_show_returns_404_for_nonexistent_slug()`: Testa 404 para slug inexistente.
12. `test_articles_show_renders_social_share_links_and_copy_button()`: Valida links sociais codificados e cópia de URL.
13. `test_articles_show_renders_recommended_articles_excluding_current()`: Valida artigos recomendados no rodapé.
14. `test_author_profile_can_be_accessed_by_slug()`: Valida rota `/autores/{slug}`.
15. `test_author_profile_can_be_accessed_by_id()`: Valida rota `/autores/{id}`.
16. `test_author_profile_returns_404_for_invalid_slug_or_id()`: Testa 404 para autor inexistente.
17. `test_author_profile_displays_bio_credentials_and_published_articles()`: Valida perfil completo e listagem de artigos.
18. `test_author_profile_hides_draft_articles_from_author_feed()`: Garante que rascunhos não vazam no perfil do autor.
19. `test_navbar_highlights_artigos_as_active_when_browsing_articles()`: Valida `aria-current="page"` em Artigos no feed e na leitura.
