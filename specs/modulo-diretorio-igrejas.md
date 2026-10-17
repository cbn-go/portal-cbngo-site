# Especificação Técnica (Spec) — Issue #6: Guia e Diretório de Igrejas Batistas Nacionais em Goiás

- **Issue:** [#6 — [WDLC-3: Public] Guia e Diretório de Igrejas Batistas Nacionais em Goiás](https://github.com/cbn-go/portal-cbngo-site/issues/6)
- **Fase WDLC:** Fase 3 (Desenvolvimento do Portal Público)
- **Metodologia:** Spec-Driven Development (SDD) com ciclo TDD Mandatório
- **Data:** 17 de Outubro de 2026
- **Status:** Implementado e Validado (Green TDD)

---

## 1. Objetivo & Escopo

### 1.1 Contexto & Objetivo
Desenvolver o diretório público de congregações batistas nacionais filiadas à Convenção Batista Nacional do Estado de Goiás (CBN-GO) na rota `/igrejas`. O módulo deve facilitar a localização rápida de igrejas em todos os municípios goianos por meio de busca textual (nome ou bairro), filtro por cidade, cards institucionais detalhados (com rotas para Google Maps e Waze, pastor titular, contatos, redes sociais e horários de cultos) e um **Mapa Interativo do Estado de Goiás** sincronizado com os filtros.

### 1.2 Goals (Dentro do Escopo)
- **Página `/igrejas` — Guia de Congregações (Sub-issue 6.1):**
  - Campo de busca rápida (`q`) pesquisando por nome da congregação (`name`) ou bairro/setor (`neighborhood`).
  - Dropdown (`cidade`) populado dinamicamente com as cidades goianas que possuem congregações ativas cadastradas, ordenadas alfabeticamente.
  - Paginação limpa (9 congregações por página na listagem de cards) preservando os parâmetros de consulta (`?q=...&cidade=...&page=2`).
  - Destaque ativo no `<x-navbar>` para o item "Igrejas" com atributo `aria-current="page"`.
  - Tratamento de estado vazio (*empty state*) amigável quando nenhuma congregação corresponder aos filtros, com ação para limpar filtros.
- **Cards Detalhados das Igrejas (`<x-church-card>`) (Sub-issue 6.2):**
  - Nome da congregação e logotipo (`logo_url`), com fallback visual institucional (`data-testid="church-logo-fallback"`) quando não houver logo.
  - Nome do pastor titular (`pastor_name`).
  - Endereço completo (`address`, `neighborhood`, `city`, `state`, `zip_code`) com links diretos de rota para **Google Maps** e **Waze** (utilizando coordenadas quando válidas ou endereço textual codificado via RFC 3986 como fallback).
  - Telefones de contato (`phone`), e-mail institucional validado (`getSafeEmail()`) e redes sociais da congregação (`social_links` sanitizados contra XSS).
  - Lista estruturada de horários regulares de cultos e reuniões de oração (`service_times`).
  - Botão de interação "Ver no Mapa" (quando a igreja possuir coordenadas válidas) para focar/destacar o marcador correspondente no Mapa de Goiás.
- **Mapa Interativo de Goiás — Presença da CBN-GO (Sub-issue 6.3):**
  - Mapa interativo e responsivo do estado de Goiás (`id="mapa-goias"`, SVG vetorial estilizado com projeção geográfica calibrada para o *bounding box* de Goiás + controle reativo em Alpine.js).
  - Exibição dos marcadores (`data-church-marker="{slug}"`) de **todas** as congregações ativas filtradas que possuem coordenadas válidas (`latitude` e `longitude`), sem corte de paginação, oferecendo visão estadual completa do filtro ativo.
  - Sincronização com os filtros de busca (`q`) e cidade (`cidade`): o mapa exibe exclusivamente os marcadores das congregações que correspondem aos filtros ativos, além de resumo estatístico de presença (total de igrejas e municípios filtrados).
  - Interatividade bidirecional: clicar em um marcador no mapa seleciona a congregação no painel lateral do mapa e, caso o card correspondente (`#igreja-{slug}`) esteja na página atual da listagem, destaca e rola suavemente até ele.
  - Experiência responsiva em mobile e desktop (com controles de visualização/alternância que nunca bloqueiam a navegação pela listagem).
  - Resiliência: congregações sem coordenadas válidas (`null` ou fora do *bounding box* geográfico de Goiás) são omitidas apenas da plotagem de pinos do mapa, permanecendo normalmente na listagem de cards sem quebrar a página.
- **Modelagem, Factory & Seeder:**
  - Migration `create_churches_table`, Model `Church`, `ChurchFactory` e atualização do `DatabaseSeeder` com congregações representativas em Goiás.

### 1.3 Non-Goals (Fora do Escopo)
- Painel administrativo (CRUD de igrejas), que pertence ao escopo administrativo (`admin.cbngo.com.br`).
- Geocodificação automática via chamadas HTTP externas em tempo de execução.
- Páginas individuais para cada igreja (`/igrejas/{slug}`).

---

## 2. Contratos & Modelagem

### 2.1 Esquema de Dados (Database Schema)

**Migração `2026_10_17_090001_create_churches_table.php`:**
```php
Schema::create('churches', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->string('logo_url')->nullable();
    $table->string('pastor_name');
    $table->string('address');
    $table->string('neighborhood');
    $table->string('city', 120);
    $table->string('state', 2)->default('GO');
    $table->string('zip_code', 20)->nullable();
    $table->decimal('latitude', 10, 7)->nullable();
    $table->decimal('longitude', 10, 7)->nullable();
    $table->string('phone', 50)->nullable();
    $table->string('email')->nullable();
    $table->json('social_links')->nullable();
    $table->json('service_times')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->index(['is_active', 'city']);
    $table->index(['is_active', 'neighborhood']);
});
```

### 2.2 Métodos de Domínio em `App\Models\Church`

- `scopeActive(Builder $query): Builder`
- `hasValidCoordinates(): bool` — Verifica coordenadas numéricas dentro do *bounding box* geográfico de Goiás (`MIN_LAT = -19.60` a `MAX_LAT = -12.35`, `MIN_LNG = -53.30` a `MAX_LNG = -45.85`), unificado com a projeção do mapa.
- `getMapPosition(): ?array{x: float, y: float}` — Projeta `(latitude, longitude)` em coordenadas percentuais `(x, y)` no mapa vetorial de Goiás usando os mesmos limites `MIN_LAT..MAX_LAT` e `MIN_LNG..MAX_LNG`.
- `getFullAddress(): string`
- `getGoogleMapsUrl(): string`
- `getWazeUrl(): string`
- `getSafeEmail(): ?string` — Valida o e-mail institucional via `FILTER_VALIDATE_EMAIL` antes de compor links `mailto:`.
- `getSafeSocialLinks(): array<string, string>`

### 2.3 Rotas & Controller

Em `routes/web.php`:
```php
Route::get('/igrejas', [ChurchController::class, 'index'])->name('igrejas');
```

Os marcadores do mapa (`$mapChurches`) contêm todas as congregações ativas que atendem aos filtros atuais (`q` e `cidade`) e possuem coordenadas válidas (`hasValidCoordinates()`), enquanto `$churches` contém a listagem paginada (9 cards por página).

---

## 3. Casos de Borda & Falhas (Edge Cases)

1. **Congregação sem coordenadas ou fora da faixa geográfica válida:** omitida dos marcadores do mapa (`hasValidCoordinates() === false`), mas listada normalmente nos cards com rotas baseadas no endereço textual codificado.
2. **Congregação inativa (`is_active = false`):** omitida da listagem, do mapa e do dropdown de cidades.
3. **Congregação sem logotipo (`logo_url = null`):** exibe fallback visual institucional (`data-testid="church-logo-fallback"`).
4. **Proteção XSS em `social_links` e validação de `email`:** `getSafeSocialLinks()` permite apenas esquemas `http://` e `https://`, e `getSafeEmail()` valida o endereço via `FILTER_VALIDATE_EMAIL`.
5. **Busca ou filtro sem resultados:** exibe empty state amigável com botão de limpeza de filtros.

---

## 4. Critérios de Aceitação & Plano de Verificação

- [x] **6.1:** Página `/igrejas` com busca rápida por nome da igreja ou bairro e dropdown com as cidades goianas cadastradas.
- [x] **6.2:** Cards detalhados das igrejas com nome, logotipo (ou fallback), pastor titular, endereço completo com rotas Google Maps/Waze, telefone, e-mail, redes sociais e horários regulares de cultos/oração.
- [x] **6.3:** Mapa interativo de Goiás exibindo todas as congregações filtradas com coordenadas válidas, sincronizado com busca/cidade, clique em marcador vinculado ao card, responsivo em mobile/desktop e resiliente a congregações sem coordenadas.

Suite `tests/Feature/ChurchDirectoryTest.php` cobrindo 100% dos critérios de aceitação (6.1, 6.2 e 6.3).
