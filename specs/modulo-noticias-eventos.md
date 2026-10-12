# Especificação Técnica (Spec) — Issue #4: Módulo de Notícias e Eventos

- **Issue:** [#4 — [WDLC-3: Public] Módulo de Notícias e Eventos](https://github.com/cbn-go/portal-cbngo-site/issues/4)
- **Fase WDLC:** Fase 3 (Desenvolvimento do Portal Público)
- **Metodologia:** Spec-Driven Development (SDD) com ciclo TDD
- **Data:** 12 de Outubro de 2026
- **Status:** Proposta para Aprovação

---

## 1. Objetivo & Escopo

### 1.1 Contexto & Objetivo
Construir o feed público de notícias (`/noticias`) e a página detalhada de leitura (`/noticias/{slug}`) do portal da Convenção Batista Nacional do Estado de Goiás (CBN-GO), proporcionando aos membros, congregações e visitantes uma experiência rápida, acessível (WCAG 2.1 AA) e informativa para acompanhar comunicados oficiais, eventos e ações ministeriais.

### 1.2 Goals (Dentro do Escopo)
- **Feed Geral (`/noticias`):**
  - Grid responsivo de notícias com paginação preservando parâmetros de consulta via query string (`?page=2&q=...`).
  - Barra de busca textual full-text/substring em títulos, resumos e conteúdos.
  - Filtros dinâmicos por município goiano (`city`) e congregação emissora (`church_name`).
  - Destaque visual e semântico para Comunicados Oficiais emitidos pela Diretoria da convenção (`is_official = true`).
  - Tratamento de estado vazio (*empty state*) com feedback intuitivo e botão de limpeza de filtros.
- **Detalhes da Notícia (`/noticias/{slug}`):**
  - Cabeçalho editorial completo com título, data formatada em português, congregação de origem, cidade e categoria.
  - Imagem de capa em alta definição com fallback visual em SVG/gradiente institucional.
  - Corpo da matéria com formatação tipográfica editorial e proteção contra XSS (*sanitização de conteúdo*).
  - Galeria de fotos do evento com miniaturas responsivas e lightbox interativo acessível em Alpine.js (fechamento por teclado ESC, navegação Anterior/Próxima e clique fora).
  - Botões de compartilhamento social direto para WhatsApp, Facebook, Twitter/X e cópia de link via Clipboard API com feedback imediato.
  - Seção de matérias relacionadas / recentes no rodapé para incentivar navegação contínua.
- **Modelagem & Banco de Dados:**
  - Migração para adicionar suporte a fotos de galeria na tabela `news` (`gallery_images` como JSON nullable).
  - Casts e métodos auxiliares no Model `News`.
- **Qualidade & Garantia:**
  - Bateria completa de testes automatizados de Feature em `tests/Feature/NewsTest.php` cobrindo 100% dos critérios e edge cases antes da implementação de produção.
  - Conformidade com PHPStan nível 5 e Laravel Pint.

### 1.3 Non-Goals (Fora do Escopo)
- Painel administrativo / CMS para publicação e upload de notícias (escopo de módulo administrativo / Fase subsequente).
- Sistema de comentários ou interações abertas de usuários não autenticados.
- Integração de feeds RSS/Atom ou Newsletter (previsto para fase de distribuição/SEO).
- Módulo de Artigos e Articulistas (pertence à Issue #5).

---

## 2. Contratos & Modelagem

### 2.1 Esquema de Dados (Database Schema)

Nova migração `database/migrations/YYYY_MM_DD_HHMMSS_add_gallery_images_to_news_table.php`:
```php
Schema::table('news', function (Blueprint $table) {
    $table->json('gallery_images')->nullable()->after('image_url');
});
```

Estrutura completa do registro `news`:
| Campo | Tipo | Nulo | Descrição |
|---|---|---|---|
| `id` | `bigint unsigned` | Não | Chave primária |
| `title` | `varchar(255)` | Não | Título da notícia |
| `slug` | `varchar(255)` | Não | Slug único para URL amigável |
| `category` | `varchar(100)` | Não | Categoria (ex: 'Comunicado Oficial', 'Missões', 'Eventos') |
| `excerpt` | `text` | Não | Resumo informativo |
| `content` | `longtext` | Não | Conteúdo integral |
| `image_url` | `varchar(255)` | Sim | Imagem de capa principal |
| `gallery_images` | `json` | Sim | Lista de URLs de fotos adicionais para o lightbox |
| `church_name` | `varchar(255)` | Sim | Nome da congregação emissora |
| `city` | `varchar(100)` | Sim | Município em Goiás |
| `is_official` | `tinyint(1)` | Não | `true` se emitido pela Diretoria Estadual |
| `is_published` | `tinyint(1)` | Não | `true` para matérias publicadas |
| `published_at` | `timestamp` | Sim | Data e hora de publicação |
| `created_at` / `updated_at` | `timestamps` | Sim | Auditoria temporal padrão |

### 2.2 Model `App\Models\News`
Atualização com novos atributos e casts:
```php
protected $fillable = [
    'title',
    'slug',
    'category',
    'excerpt',
    'content',
    'image_url',
    'gallery_images',
    'church_name',
    'city',
    'is_official',
    'is_published',
    'published_at',
];

protected $casts = [
    'gallery_images' => 'array',
    'is_official' => 'boolean',
    'is_published' => 'boolean',
    'published_at' => 'datetime',
];
```

### 2.3 Rotas & Assinaturas de Controller
Em `routes/web.php`:
```php
Route::get('/noticias', [NewsController::class, 'index'])->name('noticias.index');
Route::get('/noticias/{slug}', [NewsController::class, 'show'])->name('noticias.show');
```

Assinatura do `App\Http\Controllers\NewsController`:
```php
namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsController extends Controller
{
    /**
     * Exibe a listagem pública de notícias com busca e filtros.
     */
    public function index(Request $request): View;

    /**
     * Exibe os detalhes e leitura da notícia com lightbox e compartilhamento.
     */
    public function show(string $slug): View;
}
```

### 2.4 Parâmetros do Feed (`/noticias`)
| Parâmetro | Tipo | Exemplo | Finalidade |
|---|---|---|---|
| `q` | `string` | `"assembleia"` | Termo para busca textual em título, resumo e conteúdo |
| `cidade` | `string` | `"Anápolis"` | Filtro por município cadastrado |
| `igreja` | `string` | `"IBN Central"` | Filtro por congregação emissora |
| `apenas_oficiais`| `boolean` | `"1"` | Filtro opcional para exibir somente comunicados oficiais |
| `page` | `int` | `2` | Número da página na paginação limpa |

---

## 3. Casos de Borda & Falhas (Edge Cases)

1. **Notícia não publicada ou com publicação futura:**
   - **Cenário:** Matéria com `is_published = false` ou `published_at > now()`.
   - **Comportamento:** Acesso a `/noticias/{slug}` retorna HTTP `404 Not Found`. A matéria nunca aparece no feed nem nas notícias relacionadas.
2. **Slug inexistente:**
   - **Cenário:** Acesso a `/noticias/slug-que-nao-existe`.
   - **Comportamento:** Retorna HTTP `404 Not Found`.
3. **Busca ou filtros sem resultados:**
   - **Cenário:** Termo de busca sem correspondência ou combinação de cidade/igreja sem registros.
   - **Comportamento:** Retorna HTTP `200 OK` com interface de empty state elegante, informando que nenhuma notícia foi encontrada com os filtros selecionados e oferecendo botão para limpar filtros.
4. **Notícia sem imagem de capa (`image_url == null`):**
   - **Cenário:** Notícia de texto simples.
   - **Comportamento:** Exibe fallback gráfico elegante com gradiente de marca CBN-GO e SVG institucional, mantendo consistência visual sem quebra de layout.
5. **Notícia sem galeria de fotos (`gallery_images == null` ou vazio `[]`):**
   - **Cenário:** Matéria comum sem fotos adicionais.
   - **Comportamento:** A seção de galeria e o lightbox são completamente omitidos do DOM, sem erros de JavaScript ou marcação vazia.
6. **Galeria com foto única:**
   - **Cenário:** `gallery_images` possui exatamente 1 imagem.
   - **Comportamento:** Lightbox abre normalmente sem quebras; controles de navegação anterior/próxima são desabilitados ou ocultados.
7. **Injeção de caracteres especiais ou scripts na busca:**
   - **Cenário:** Busca contendo aspas, símbolos ou tags (`<script>`).
   - **Comportamento:** Query builder utiliza PDO parameter bindings (sem risco de SQL injection). Na view, caracteres são escapados com Blade `{{ $q }}` prevenindo XSS.
8. **Sanitização do conteúdo da notícia:**
   - **Cenário:** Notícia possui quebras de linha (`\n`) ou parágrafos.
   - **Comportamento:** Renderização estruturada garantindo parágrafos sem permitir tags HTML arbitrárias maliciosas.
9. **Compartilhamento social com caracteres especiais e acentos:**
   - **Cenário:** Título da notícia contém pontuação, emojis e acentos (ex: "42ª Assembleia Geral em Goiás: Comunhão & Fé").
   - **Comportamento:** URLs para WhatsApp, Twitter/X e Facebook são formatadas com codificação RFC 3986 (`urlencode`), garantindo que o link e texto sejam transmitidos sem corrupção.

---

## 4. Critérios de Aceitação & Plano de Verificação

### 4.1 Critérios de Aceitação (Checklist da Issue #4)

- [ ] **Feed Geral (`/noticias`):**
  - [ ] Rota `/noticias` responde com HTTP 200 e layout oficial integrado.
  - [ ] Paginação exibe até 9 notícias por página e mantém parâmetros de busca e filtros nas URLs dos botões de navegação.
  - [ ] Campo de busca textual filtra eficientemente por título, resumo e conteúdo.
  - [ ] Filtro por município goiano (`city`) selecionável e funcional.
  - [ ] Filtro por congregação emissora (`church_name`) selecionável e funcional.
  - [ ] Comunicados oficiais da diretoria possuem destaque visual e badge clara de distinção.
  - [ ] Empty state amigável exibido quando nenhum resultado é retornado.
  - [ ] Active state destacado no navbar para "Notícias".

- [ ] **Detalhes da Notícia (`/noticias/{slug}`):**
  - [ ] Rota `/noticias/{slug}` responde com HTTP 200 para notícias publicadas.
  - [ ] Exibe imagem de capa em alta resolução ou fallback elegante.
  - [ ] Exibe metadados de publicação (data formatada em português, congregação de origem, cidade de Goiás).
  - [ ] Exibe badge de Comunicado Oficial caso `is_official == true`.
  - [ ] Conteúdo do artigo é formatado e protegido contra vulnerabilidades.
  - [ ] Seção de galeria de fotos renderizada quando `gallery_images` estiver presente.
  - [ ] Lightbox interativo funcional via Alpine.js com visualização em tela cheia, navegação entre fotos e fechamento via ESC / clique externo.
  - [ ] Botões de compartilhamento social presentes e operantes (WhatsApp, Facebook, Twitter/X, Copiar Link com feedback de clipboard).
  - [ ] Seção de notícias recomendadas / recentes no rodapé da página.
  - [ ] Responde com 404 para matérias não publicadas, agendadas para o futuro ou slugs inválidos.

### 4.2 Plano de Verificação Automatizada (Testes TDD)

Será criado o arquivo `tests/Feature/NewsTest.php` com os seguintes testes unitários e de integração:

1. `test_news_index_displays_published_news_and_pagination()`: Valida renderização do feed e paginação de 9 itens.
2. `test_news_index_hides_draft_and_future_news()`: Garante que rascunhos e notícias futuras não vazam para o feed.
3. `test_news_index_can_search_by_title_and_content()`: Testa a busca textual por palavras-chave.
4. `test_news_index_can_filter_by_city()`: Testa o filtro por cidade de Goiás.
5. `test_news_index_can_filter_by_church()`: Testa o filtro por igreja emissora.
6. `test_news_index_highlights_official_announcements()`: Valida identificadores e badges de comunicados oficiais.
7. `test_news_index_shows_empty_state_when_no_results_match_filters()`: Valida empty state e botão de limpar filtros.
8. `test_news_show_displays_published_news_details_and_metadata()`: Valida visualização da matéria, data formatada, cidade e igreja.
9. `test_news_show_returns_404_for_unpublished_or_future_news()`: Testa barreira 404 para não publicadas.
10. `test_news_show_returns_404_for_nonexistent_slug()`: Testa 404 para slug inexistente.
11. `test_news_show_renders_photo_gallery_when_gallery_images_present()`: Valida marcação do lightbox e galeria de fotos.
12. `test_news_show_renders_social_share_links_with_correct_encoded_urls()`: Testa links gerados para WhatsApp, Twitter, Facebook e botão de copiar link.
13. `test_news_show_renders_related_news_excluding_current()`: Valida recomendação de notícias excluindo a atual.
14. `test_navbar_highlights_noticias_as_active_when_browsing_news()`: Valida a navegação ativa no menu.
