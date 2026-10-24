# Especificação Técnica (Spec) — Issue #20: Exibição de mídia vinda do storage compartilhado

- **Issue:** [#20 — [Integração] Exibição de mídia vinda do storage compartilhado](https://github.com/cbn-go/portal-cbngo-site/issues/20)
- **Plano de Origem:** `C:\Users\fboli\.cursor\plans\mídia_compartilhada_portal_7001b480.plan.md`
- **Fase WDLC:** Integração Painel ↔ Portal Público (Mídia Compartilhada)
- **Metodologia:** Spec-Driven Development (SDD) com ciclo TDD Mandatório
- **Data:** 24 de Outubro de 2026
- **Status:** Proposta para Aprovação

---

## 1. Objetivo & Escopo

### 1.1 Contexto & Objetivo
A Issue [#19](https://github.com/cbn-go/portal-cbngo-site/issues/19) estabeleceu a leitura do banco compartilhado e a base de montagem de URLs via `App\Support\Media::url()` com o prefixo `MEDIA_URL` para capas, fotos, logos e arquivos.
A Issue [#20](https://github.com/cbn-go/portal-cbngo-site/issues/20) complementa essa integração com:
1. Resolução de variantes WebP (`thumb`, `card`, `cover`) geradas automaticamente pelo painel administrativo ([admin#20](https://github.com/cbn-go/portal-cbngo-admin/issues/20)).
2. Componentes visuais padronizados no Design System com `srcset` responsivo (`x-media-image`) e fallback visual unificado (`x-media-placeholder`).
3. Inclusão de imagens no `sitemap.xml` (extensão padrão `image:image` / `image:loc`) para notícias, artigos e perfis de articulistas.

### 1.2 Goals (Dentro do Escopo)
- **M.1 & M.2 (Extensão de Media):**
  - Implementar método `Media::variant(?string $path, string $size): ?string` para os tamanhos `thumb` (400px), `card` (800px) e `cover` (1600px).
  - Regra de transformação: para caminho relativo `pasta/arquivo.ext`, gera `MEDIA_URL/pasta/arquivo-{size}.webp`.
  - Se `$path` já for URL absoluta externa (http/https com host diferente do configured media url) ou esquema inseguro/inválido (`javascript:`, `data:`, `//`), a variante não é aplicada (retorna null ou a URL original quando seguro).
  - Suportar método auxiliar `Media::srcset(?string $path, array $sizes = ['thumb' => '400w', 'card' => '800w', 'cover' => '1600w']): ?string`.
  - Documentos (`documents.file`) e anexos do RichEditor permanecem apenas com a URL original e não geram variantes.
- **M.3 (Fallback Visual & Placeholders unificados do Design System):**
  - Criar componente Blade padronizado `x-media-placeholder` (`resources/views/components/media-placeholder.blade.php`), com atributo `data-testid="media-placeholder"`, aceitando variantes `card`, `cover`, `avatar`, `logo`.
  - Criar componente Blade responsivo `x-media-image` (`resources/views/components/media-image.blade.php`) que renderiza `<picture>` ou `<img>` com `src` (original como fallback para o caso das variantes ainda estarem em fila de processamento no painel), `srcset` WebP, `sizes`, `alt`, classes e suporte integrado a fallback automático quando a mídia for nula.
  - Substituir os placeholders isolados/ad-hoc existentes em:
    - Card de notícia (`news-card.blade.php`)
    - Capa de detalhe de notícia (`noticias/show.blade.php`)
    - Capa de detalhe de artigo (`artigos/show.blade.php` — atualmente ausente quando não há cover)
    - Card de artigo (`article-card.blade.php`)
    - Perfil do autor (`autores/show.blade.php`)
    - Card de igreja (`church-card.blade.php`)
    - Card de diretoria (`board-member-card.blade.php`)
  - Adicionar a seção de vitrine de Mídia e Placeholders em `/design-system` (`resources/views/design-system/index.blade.php`).
- **M.4 (Sitemap com Metatags de Mídia):**
  - No `sitemap.blade.php` e `SitemapController`, adicionar o namespace XML `xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"`.
  - Emitir bloco `<image:image><image:loc>{{ url }}</image:loc></image:image>` para notícias (`featured_image_url`), artigos (`cover_image_url`) e articulistas (`avatar_url`) quando houver mídia válida.
  - Páginas estáticas ou registros sem mídia válida não emitem a tag `<image:image>`.

### 1.3 Non-Goals (Fora do Escopo)
- Geração/redimensionamento ou conversão WebP local no portal (o portal é leitor estrito do storage gerenciado pelo painel).
- Servir arquivos binários diretamente via Laravel (mídia é servida pelo subdomínio de storage/Nginx/CDN).
- Alterar as imagens de anexos embutidos no RichEditor além da sanitização já entregue no PR #21.

---

## 2. Contratos & Modelagem

### 2.1 Contrato de Variantes WebP (`App\Support\Media`)
O painel gera para cada upload de imagem os arquivos com sufixo e extensão `.webp`:
- `{caminho_sem_extensao}-thumb.webp` (largura máxima 400px)
- `{caminho_sem_extensao}-card.webp` (largura máxima 800px)
- `{caminho_sem_extensao}-cover.webp` (largura máxima 1600px)

```php
namespace App\Support;

class Media
{
    public const SIZES = ['thumb', 'card', 'cover'];

    public static function url(?string $path): ?string;

    /**
     * Retorna a URL da variante WebP para o tamanho especificado (thumb, card, cover).
     * Retorna null se o caminho for vazio, inválido, não for imagem ou não suportar variante.
     */
    public static function variant(?string $path, string $size): ?string;

    /**
     * Monta a string do atributo srcset contendo as variantes WebP disponíveis.
     * Exemplo: "https://midia.../foto-thumb.webp 400w, https://midia.../foto-card.webp 800w, https://midia.../foto-cover.webp 1600w"
     */
    public static function srcset(?string $path, array $sizes = ['thumb' => '400w', 'card' => '800w', 'cover' => '1600w']): ?string;
}
```

### 2.2 Componente `x-media-placeholder`
Propriedades:
- `variant`: `'card'` (padrão, proporção 16:9), `'cover'` (proporção 21:9), `'avatar'` (círculo com monograma/iniciais ou ícone), `'logo'` (quadrado com ícone institucional).
- `label` / `initials`: texto opcional para monograma de avatar ou texto acessível.
- Atributo HTML obrigatório: `data-testid="media-placeholder"`.

### 2.3 Componente `x-media-image`
Propriedades:
- `path`: caminho relativo no storage (ex.: `news/featured/banner.jpg`) ou URL completa.
- `alt`: descrição da imagem (obrigatória para acessibilidade).
- `variant`: variante prioritária ou tamanho base (ex: `'card'`, `'cover'`, `'thumb'`).
- `aspect`: classe de proporção (ex: `'aspect-video'`, `'aspect-[21/9]'`, `'rounded-full'`, etc.).
- `fallbackVariant`: variante do placeholder caso `path` seja nulo (`'card'`, `'cover'`, `'avatar'`, `'logo'`).
- `fallbackInitials`: iniciais para avatar caso não haja foto.
- `loading`: `'lazy'` (padrão) ou `'eager'`.
- `fetchpriority`: `'auto'`, `'high'`, `'low'`.

Renderização:
- Se houver `path` válido: renderiza `<img>` com `src="Media::url($path)"`, `srcset="Media::srcset($path)"`, `sizes="..."` e classes CSS adequadas.
- Se `path` for nulo ou inválido: renderiza `x-media-placeholder`.

### 2.4 Contrato do Sitemap XML de Imagens
```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    <url>
        <loc>https://cbngo.org.br/noticias/convencao-2026</loc>
        <lastmod>2026-10-24T18:00:00+00:00</lastmod>
        <image:image>
            <image:loc>https://midia.cbngo.org.br/news/featured/banner-convencao.jpg</image:loc>
        </image:image>
    </url>
</urlset>
```

---

## 3. Casos de Borda & Falhas (Edge Cases)

1. **Uploads recém-feitos onde as variantes ainda não terminaram de processar no painel:**
   O atributo `src` do `<img>` aponta sempre para o arquivo original gerado imediatamente (`Media::url($path)`). O navegador usará `src` como fallback caso o srcset ainda resulte em 404 momentâneo ou não suporte WebP.
2. **Caminhos com múltiplas extensões ou sem extensão (ex.: `path/to/image.min.jpg`):**
   O regex de substituição deve capturar a última extensão após o último ponto: `preg_replace('/\.[a-zA-Z0-9]+$/', "-{$size}.webp", $path)`.
3. **Valores nulos, vazios ou strings maliciosas (`javascript:...`, `data:...`):**
   `Media::variant()` e `Media::srcset()` retornam estritamente `null`.
4. **URLs absolutas completas de servidores externos:**
   Se uma imagem já possuir URL externa absoluta (ex.: `https://images.unsplash.com/...`), variantes não são calculadas e `Media::variant()` retorna null; `x-media-image` exibe a imagem sem `srcset` quebrado.
5. **Artigo ou notícia sem imagem:**
   Não exibe imagem quebrada; exibe `x-media-placeholder` com padrão institucional consistente e não emite tag `image:image` no `sitemap.xml`.

---

## 4. Critérios de Aceitação & Plano de Verificação (TDD)

### Testes Unitários (`tests/Unit/MediaTest.php`)
- `test_media_variant_generates_webp_urls_for_thumb_card_and_cover`
- `test_media_variant_returns_null_for_empty_unsafe_or_external_urls`
- `test_media_srcset_generates_srcset_string_with_descriptors`

### Testes de Componentes e Design System (`tests/Feature/DesignSystemTest.php`)
- `test_media_placeholder_component_renders_all_variants_with_testid`
- `test_media_image_component_renders_img_with_src_and_srcset`
- `test_media_image_component_renders_placeholder_when_path_is_null`
- `test_design_system_showcase_displays_media_components`

### Testes de Feature de Mídia & Integração
- **Notícias (`tests/Feature/NewsTest.php`):**
  - Card de notícias e detalhe renderizam `x-media-placeholder` quando `featured_image` for nula.
  - Detalhe e cards renderizam `srcset` WebP quando há `featured_image`.
- **Artigos (`tests/Feature/ArticleTest.php`):**
  - Detalhe de artigo exibe placeholder quando não há capa cadastrada (não colapsa o layout).
- **Diretoria e Igrejas (`tests/Feature/InstitutionalPagesTest.php`, `tests/Feature/ChurchDirectoryTest.php`):**
  - Membros sem foto e igrejas sem logo exibem os placeholders unificados com atributo `data-testid="media-placeholder"`.
- **Sitemap (`tests/Feature/SeoTest.php`):**
  - `sitemap.xml` possui namespace `xmlns:image`.
  - Entradas de notícias, artigos e autores com imagem possuem `<image:image><image:loc>`.
  - Entradas sem imagem não possuem `<image:image>`.
