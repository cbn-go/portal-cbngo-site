# Especificação Técnica (Spec) — Issue #7: Páginas Institucionais

- **Issue:** [#7 — [WDLC-3: Public] Páginas Institucionais](https://github.com/cbn-go/portal-cbngo-site/issues/7)
- **Fase WDLC:** Fase 3 (Desenvolvimento do Portal Público)
- **Metodologia:** Spec-Driven Development (SDD) com ciclo TDD Mandatório
- **Data:** 23 de Outubro de 2026
- **Status:** Implementado e Validado (Green TDD)

---

## 1. Objetivo & Escopo

### 1.1 Contexto & Objetivo
Construir as páginas de apresentação institucional da Convenção Batista Nacional do Estado de Goiás (CBN-GO), substituindo as rotas placeholder `/quem-somos`, `/diretoria` e `/contato`. O site legado (`cbngo.com.br`) serve como fonte inicial de conteúdo; os textos de história e doutrina são publicados como **rascunho editável** (partials Blade) até a revisão oficial da secretaria, e diretoria/documentos são modelados em tabelas para posterior gestão pelo painel administrativo.

### 1.2 Goals (Dentro do Escopo)
- **Página `/quem-somos` (Sub-issue 7.1):**
  - Seção `id="historia"` com a história da CBN e sua trajetória em Goiás.
  - Seção `id="declaracao-doutrinaria"` com a Declaração Doutrinária das Igrejas Batistas Nacionais.
  - Seção `id="pacto"` com o Pacto de Fé (síntese do Pacto Renovado).
  - Seção `id="documentos"` listando os documentos oficiais ativos (`Document::active()`, ordenados por `position`) com link de consulta/download (`target="_blank"`, `rel="noopener noreferrer"`), cards `<x-document-card>` e empty state quando não houver documentos.
- **Página `/diretoria` (Sub-issue 7.2):**
  - Galeria da diretoria atual agrupada por órgão, na ordem fixa de `BoardMember::GROUPS`: Presidência, Vice-presidências, Secretaria, Tesouraria, Conselho Fiscal e Coordenadores de Regiões (`data-board-group="{chave}"`).
  - Dentro de cada grupo, membros ativos ordenados por `position` e depois `name`; grupos sem membros não são renderizados.
  - Cards `<x-board-member-card>` com foto (`photo_url` segura) ou fallback de iniciais (`data-testid="board-member-initials"`), nome, cargo e região (coordenadores).
  - Empty state quando não houver membros ativos.
- **Página `/contato` (Sub-issue 7.3):**
  - Dados oficiais da sede estadual (endereço, telefone, WhatsApp, e-mail e expediente) vindos de `config('cbngo.contact')`, com links `tel:`, `https://wa.me/{digits}`, `mailto:` e "Como chegar" (Google Maps).
  - Redes sociais oficiais vindas de `config('cbngo.social')`.
- **Fonte única de contato:** `config/cbngo.php` consumido pela página `/contato`, pelo `<x-footer>` e pela barra superior do layout. E-mail oficial: `contato@cbngo.org.br`.
- **Navbar:** item "Institucional" ativo (`aria-current="page"`) em `/quem-somos` e `/diretoria`; item "Contato" ativo em `/contato`.
- **Modelagem, Factory & Seeder:** migrations `board_members` e `documents`, models, factories e `DatabaseSeeder` com diretoria de rascunho e os documentos Estatuto, Regimento Interno e Pacto Renovado.

### 1.3 Non-Goals (Fora do Escopo)
- **Formulário institucional de contato** (decisão de produto: `/contato` exibe somente os dados oficiais).
- CRUD da diretoria e upload de documentos/fotos, que pertencem ao painel administrativo (`admin.cbngo.com.br`).
- Conteúdo oficial final (textos, nomes e fotos): os dados entregues são rascunho para revisão da secretaria.

---

## 2. Contratos & Modelagem

### 2.1 Esquema de Dados (Database Schema)

**Migração `2026_10_23_100001_create_board_members_table.php`:**
```php
Schema::create('board_members', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('role');
    $table->string('group', 40);
    $table->string('region')->nullable();
    $table->string('photo_url')->nullable();
    $table->string('term_label', 60)->nullable();
    $table->unsignedInteger('position')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->index(['is_active', 'group', 'position']);
});
```

**Migração `2026_10_23_100002_create_documents_table.php`:**
```php
Schema::create('documents', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->string('category', 40)->default('outros');
    $table->string('file_url');
    $table->string('file_format', 10)->default('PDF');
    $table->unsignedInteger('position')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->index(['is_active', 'position']);
});
```

### 2.2 Métodos de Domínio

**`App\Models\BoardMember`:**
- `GROUPS`: `array<string, string>` ordenado — `presidencia => Presidência`, `vice_presidencia => Vice-presidências`, `secretaria => Secretaria`, `tesouraria => Tesouraria`, `conselho_fiscal => Conselho Fiscal`, `coordenacao_regional => Coordenadores de Regiões`.
- `scopeActive(Builder $query): Builder`
- `getInitials(): string` — até duas iniciais em maiúsculas, ignorando títulos (`Pr.`, `Pra.`, `Dr.`, `Dra.`, `Diác.`, `Presb.`) e preposições (`da`, `de`, `do`, `das`, `dos`, `e`).
- `getSafePhotoUrl(): ?string` — apenas `http://`/`https://`.

**`App\Models\Document`:**
- `scopeActive(Builder $query): Builder`
- `getSafeFileUrl(): ?string` — aceita `http://`, `https://` ou caminho relativo iniciado por `/` (sem `//`); demais esquemas retornam `null`.

**`App\Support\ContactLinks`:** `digits()`, `tel()`, `whatsapp()` e `maps()` geram os links de contato a partir do `config/cbngo.php` (usados em `/contato` e no `<x-footer>`).

### 2.3 Rotas & Controller

Em `routes/web.php`:
```php
Route::get('/quem-somos', [InstitutionalController::class, 'about'])->name('quem-somos');
Route::get('/diretoria', [InstitutionalController::class, 'board'])->name('diretoria');
Route::get('/contato', [InstitutionalController::class, 'contact'])->name('contato');
```

No `InstitutionalController`:
- `about()` → `institucional.quem-somos` com `$documents` (`Collection<Document>` ativos por `position`, `title`).
- `board()` → `institucional.diretoria` com `$groups`: `array<string, array{label: string, members: Collection<BoardMember>}>` na ordem de `BoardMember::GROUPS`, apenas grupos com membros ativos.
- `contact()` → `institucional.contato` com `$contact` (`config('cbngo.contact')`) e `$social` (`config('cbngo.social')`).

---

## 3. Casos de Borda & Falhas (Edge Cases)

1. **Documento com `file_url` inseguro (`javascript:`, `data:`, `//host`):** o card é exibido sem link de download e com a indicação "Arquivo indisponível".
2. **Documento ou membro inativo (`is_active = false`):** omitido das páginas públicas.
3. **Membro sem foto ou com `photo_url` inseguro:** fallback de iniciais (`data-testid="board-member-initials"`).
4. **Membro com `group` desconhecido:** não é exibido (não pertence a nenhum órgão de `GROUPS`).
5. **Sem membros ou sem documentos:** empty state amigável, página responde 200.
6. **WhatsApp com máscara (`(62) 98487-6863`):** o link `wa.me` usa apenas dígitos com DDI `55`.
7. **Colisão de âncora:** o `<footer>` não usa mais `id="contato"`.

---

## 4. Critérios de Aceitação & Plano de Verificação

### 4.1 Critérios de Aceitação (Checklist da Issue #7)
- [x] **7.1:** Página `/quem-somos`:
  - [x] História da CBN em Goiás e sua trajetória pioneira.
  - [x] Declaração Doutrinária e Pacto de Fé das Igrejas Batistas Nacionais.
  - [x] Estatuto e Regimento Interno disponíveis para consulta/download.
- [x] **7.2:** Página `/diretoria` com galeria da diretoria atual e órgãos da convenção.
- [x] **7.3:** Página `/contato` com dados oficiais da sede estadual (endereço, telefone, WhatsApp, expediente).
  - Formulário institucional: **fora do escopo** por decisão de produto (ver 1.3).

### 4.2 Plano de Verificação Automatizada (Testes TDD)
Suite `tests/Feature/InstitutionalPagesTest.php`:
1. `test_about_page_renders_history_doctrine_and_covenant_sections()`: seções 7.1.
2. `test_about_page_lists_active_documents_with_download_links()`: documentos ativos com link; inativos omitidos.
3. `test_about_page_shows_empty_state_without_documents()`.
4. `test_about_page_does_not_link_unsafe_document_urls()`: proteção XSS.
5. `test_board_page_groups_members_in_institutional_order()`: ordem dos órgãos e `position`.
6. `test_board_page_hides_inactive_members_and_unknown_groups()`.
7. `test_board_member_card_renders_initials_fallback_and_region()`.
8. `test_board_page_shows_empty_state_without_members()`.
9. `test_contact_page_renders_official_contact_data_from_config()`: endereço, `tel:`, `wa.me`, `mailto:`, expediente; sem `<form>`.
10. `test_navbar_highlights_institutional_on_about_and_board_pages()`.
11. `test_navbar_highlights_contact_on_contact_page()`.
12. `test_layout_uses_official_contact_email_everywhere()`: sem `secretaria@cbngo.org.br` nem `contato@cbngo.com.br`.
13. `test_database_seeder_populates_board_members_and_documents()`.
