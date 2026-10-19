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
  - Botão/link de interação no card para filtrar ou visualizar o município no mapa estadual.
- **Mapa Coroplético Municipal de Goiás — Presença da CBN-GO (Sub-issue 6.3):**
  - Mapa coroplético interativo e responsivo dos municípios do estado de Goiás (~246 municípios da malha IBGE) em `id="mapa-goias"`, renderizado via **Leaflet** (`data-mapa="municipios"`, container `id="leaflet-mapa-goias"`) consumindo o asset estático `/geo/goias-municipios.geojson`.
  - Destaque visual claro entre municípios **com presença** (cor de marca laranja CBN `#E56B20`) e municípios **sem presença** (cinza neutro `#E5E7EB`), sem pinos individuais (`data-church-marker`) no mapa estadual.
  - Sincronização com os filtros de busca (`q`) e cidade (`cidade`): o controller injeta `presenceByCity` (`array<string, int>` com a contagem de congregações ativas filtradas por município, sem corte de paginação). Apenas os municípios com congregações no resultado filtrado recebem destaque "com presença"; os demais permanecem como "sem presença", mantendo a visão estadual completa.
  - Interatividade municipal:
    - Clicar em um município **com** igreja aplica o filtro `cidade` correspondente na listagem (`?cidade=Nome`).
    - Clicar ou passar o cursor em um município **sem** igreja exibe a indicação `"Sem congregação filiada"`.
    - Painel lateral com lista rápida dos municípios com presença no filtro atual (com contagem de igrejas e link direto para filtrar por `?cidade=Nome`).
  - Legenda explícita (`Com congregação filiada` / `Sem congregação filiada`) e contadores de cobertura estadual (`X / 246 municípios` com presença e total de congregações filtradas).
  - Experiência responsiva em mobile e desktop (com toggle `Expandir mapa` / `Recolher mapa` que nunca bloqueia a navegação pela listagem).
  - Resiliência: congregações sem coordenadas válidas (`latitude`/`longitude` nulas ou fora do *bounding box* de Goiás) continuam contabilizadas na presença do seu município (`city`) e aparecem normalmente na listagem de cards com rotas Google Maps/Waze baseadas no endereço textual, sem quebrar a página nem o mapa.
- **Modelagem, Factory & Seeder:**
  - Migration `create_churches_table`, Model `Church`, `ChurchFactory` e atualização do `DatabaseSeeder` com congregações representativas em Goiás.

### 1.3 Non-Goals (Fora do Escopo)
- Painel administrativo (CRUD de igrejas), que pertence ao escopo administrativo (`admin.cbngo.com.br`).
- Geocodificação automática via chamadas HTTP externas em tempo de execução.
- Páginas individuais para cada igreja (`/igrejas/{slug}`).
- Pinos individuais por coordenada no mapa estadual ou mapa de calor contínuo por densidade (além do destaque coroplético com/sem presença e contagem no tooltip/painel).

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
- `hasValidCoordinates(): bool` — Verifica coordenadas numéricas dentro do *bounding box* geográfico de Goiás (`MIN_LAT = -19.60` a `MAX_LAT = -12.35`, `MIN_LNG = -53.30` a `MAX_LNG = -45.85`) para priorizar coordenadas nas rotas externas Google Maps/Waze.
- `getFullAddress(): string`
- `getGoogleMapsUrl(): string`
- `getWazeUrl(): string`
- `getSafeEmail(): ?string` — Valida o e-mail institucional via `FILTER_VALIDATE_EMAIL` antes de compor links `mailto:`.
- `getSafeSocialLinks(): array<string, string>`

### 2.3 Rotas, Controller & Contrato do Mapa Coroplético

Em `routes/web.php`:
```php
Route::get('/igrejas', [ChurchController::class, 'index'])->name('igrejas');
```

No `ChurchController@index`:
- `$churches`: listagem paginada (9 cards por página) das congregações ativas filtradas por `q` e `cidade`.
- `$presenceByCity`: `array<string, int>` mapeando o nome do município (`city` com `trim`) para a quantidade de congregações ativas que atendem aos filtros atuais (`q` e `cidade`), ordenado por nome do município, sem corte de paginação.
- `$totalMunicipalities`: constante `246` representando o total de municípios do Estado de Goiás (malha IBGE).
- `<x-goias-map>` expõe:
  - `id="mapa-goias"` com `data-mapa="municipios"`, `data-geojson-url="/geo/goias-municipios.geojson"`, `data-presence-by-city` (JSON de `$presenceByCity`) e `data-filter-base-url="{{ route('igrejas') }}"`.
  - Legenda explícita de presença (`Com congregação filiada` e `Sem congregação filiada`) e contador `{{ count($presenceByCity) }} / 246 municípios`.

---

## 3. Casos de Borda & Falhas (Edge Cases)

1. **Congregação sem coordenadas ou fora da faixa geográfica válida:** permanece contabilizada normalmente no município (`$presenceByCity`) e listada nos cards com rotas Google Maps/Waze baseadas no endereço textual codificado, sem quebrar o mapa.
2. **Congregação inativa (`is_active = false`):** omitida da listagem, de `$presenceByCity` no mapa e do dropdown de cidades.
3. **Congregação sem logotipo (`logo_url = null`):** exibe fallback visual institucional (`data-testid="church-logo-fallback"`).
4. **Proteção XSS em `social_links` e validação de `email`:** `getSafeSocialLinks()` permite apenas esquemas `http://` e `https://`, e `getSafeEmail()` valida o endereço via `FILTER_VALIDATE_EMAIL`.
5. **Busca ou filtro sem resultados:** exibe empty state amigável na listagem e o mapa mantém os 246 municípios em estado `"Sem congregação filiada"` (`0 / 246 municípios`).
6. **Normalização de nomes de municípios no GeoJSON:** comparação no frontend normaliza acentos, caixa (`toLowerCase()`) e espaços extras (`trim()`) para garantir correspondência entre `feature.properties.name` do GeoJSON IBGE e as chaves de `presenceByCity`.

---

## 4. Critérios de Aceitação & Plano de Verificação

- [x] **6.1:** Página `/igrejas` com busca rápida por nome da igreja ou bairro e dropdown com as cidades goianas cadastradas.
- [x] **6.2:** Cards detalhados das igrejas com nome, logotipo (ou fallback), pastor titular, endereço completo com rotas Google Maps/Waze, telefone, e-mail, redes sociais e horários regulares de cultos/oração.
- [x] **6.3:** Mapa coroplético municipal de Goiás (GeoJSON IBGE + Leaflet) destacando municípios com e sem congregação filiada, legenda + contadores (`X / 246 municípios`), sincronizado com busca/cidade, clique em município filtrando a cidade e resiliente a congregações sem coordenadas.

Suite `tests/Feature/ChurchDirectoryTest.php` cobrindo 100% dos critérios de aceitação (6.1, 6.2 e 6.3).

