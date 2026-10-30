# Especificação Técnica (Spec) — Issue #39: Hero da home com carrossel de avisos 1:1

- **Issue:** [#39 — [UI] Nova hero da home: carrossel de avisos com foto 1:1 e texto ao lado](https://github.com/cbn-go/portal-cbngo-site/issues/39)
- **Painel:** [admin#46 — Avisos da Home: foto 1:1 com selo, título, subtítulo e data](https://github.com/cbn-go/portal-cbngo-admin/issues/46) (PR admin#47)
- **Substitui:** o comportamento da #24 (arte 16:9 inteira, sem texto)
- **Mockup:** [`docs/mockups/home-avisos-1x1.html`](../docs/mockups/home-avisos-1x1.html)
- **Fase WDLC:** Integração Painel ↔ Portal Público
- **Metodologia:** Spec-Driven Development (SDD) com ciclo TDD Mandatório
- **Data:** 30 de Outubro de 2026
- **Status:** Aprovada (mockup aprovado em 30/10)

---

## 1. Objetivo & Escopo

A hero da home passa a ser um carrossel de **avisos** com foto quadrada 1:1 à direita e o texto à esquerda: selo, título, subtítulo, botão e uma linha de data e local. **Só a hero muda.** As demais seções da home, a hero institucional de reserva (sem avisos) e as outras páginas continuam iguais.

### 1.1 Goals
- **Desktop (≥ 1024px):**
  - grade de 12 colunas em `max-w-7xl`;
  - foto 1:1 de até 560px à direita, com selo e contador "1 / N";
  - texto à esquerda: marcador "Avisos da Convenção", título `h2`, subtítulo, botão e `meta`.
- **Celular:** foto primeiro, ocupando a largura, com o texto e a navegação abaixo. Sem rolagem horizontal.
- **Fundo:** `bg-cbn-sand` com a foto ativa desfocada (25%) e gradiente até `cbn-cream`, decorativo.
- **Navegação** (2 ou mais avisos):
  - miniaturas 1:1 com barra de progresso;
  - anterior, próximo e pausar;
  - teclas ← e →;
  - swipe.
- **Comportamento:**
  - troca a cada 7 s;
  - pausa com o mouse em cima, com o foco dentro do carrossel e pelo botão;
  - com `prefers-reduced-motion`: sem autoplay e sem zoom.
- **Casos:**
  - 1 aviso: sem navegação e sem contador;
  - 0 avisos: hero de reserva;
  - sem link válido: sem botão;
  - campo vazio: o elemento não aparece.
- **Acessibilidade:**
  - um único `h1` na página (o `sr-only` da home), e os títulos dos avisos são `h2`;
  - slides inativos com `aria-hidden` e `inert`;
  - alvos de toque de 44px.
- **Imagens:**
  - variantes WebP quadradas `thumb` 160, `card` 800 e `cover` 1080;
  - primeira foto com `fetchpriority="high"`, as demais com `lazy`.

### 1.2 Non-Goals
- Cadastro, recorte 1:1 e variantes (painel, admin#46).
- Mudanças nas outras seções da home.

---

## 2. Contratos

### 2.1 Tabela `banners` (espelho do painel)
Colunas novas da migration `2026_10_30_160000_add_1x1_fields_to_banners_table`:
- `tag` (30), `subtitle` (160) e `meta` (60), todas opcionais;
- `is_square` (boolean, padrão true). Os banners 16:9 antigos ficaram com `is_square=false` e `is_active=false`.

`title` passa a ser exibido. `link_label` é o texto do botão, com padrão "Saiba mais".

### 2.2 `App\Models\Banner`
- `scopeVisible()`: ativo, vigente e `is_square = true`, na ordem de `position` e depois `id`.
- Accessors:
  - `image_url`: variante `cover`, com fallback para o original;
  - `image_srcset`: `thumb` 160w, `card` 800w e `cover` 1080w;
  - `thumb_url`;
  - `link`, `opens_new_tab`;
  - `button_label`.
- `WIDTH` e `HEIGHT` = 1080.

### 2.3 `<x-hero-banner :banners>`
Usa o `heroCarousel` de `resources/js/hero-carousel.js`, sem mudança de lógica. O cache de `HomeController::banners()` é mantido, com chave por `max(updated_at)` e `count(*)`.

---

## 3. Testes
- **`tests/Feature/HomeBannerTest.php`:**
  - o aviso mostra título `h2`, subtítulo, selo, botão, `meta`, `srcset` 1:1 e `fetchpriority`;
  - campos vazios não geram marcação;
  - 1 aviso aparece sem navegação;
  - 3 avisos seguem a ordem, com miniaturas e contador;
  - legado, inativo, agendado e expirado ficam ocultos, e a reserva aparece sem avisos;
  - há um único `h1`;
  - o `target` do link segue o destino;
  - o cache é renovado após edição;
  - as seções seguintes ficam idênticas com e sem avisos.
- **`tests/Unit/BannerTest.php`:** escopo com `is_square`, `srcset` 1:1 e `button_label`.
