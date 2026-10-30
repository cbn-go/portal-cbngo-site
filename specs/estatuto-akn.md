# Especificação Técnica (Spec) — Issue #33: Página /estatuto a partir do Akoma Ntoso 3.0

- **Issue:** [#33 — [Integração] Página /estatuto renderizada a partir do Akoma Ntoso 3.0](https://github.com/cbn-go/portal-cbngo-site/issues/33)
- **Painel:** [admin#39 — modelo, validação e carga inicial](https://github.com/cbn-go/portal-cbngo-admin/issues/39) e [admin#40 — editor estruturado](https://github.com/cbn-go/portal-cbngo-admin/issues/40)
- **Fase WDLC:** Integração Painel ↔ Portal Público
- **Metodologia:** Spec-Driven Development (SDD) com ciclo TDD Mandatório
- **Data:** 30 de Outubro de 2026
- **Status:** Aprovada (mockup `tmp/mockup-estatuto.html` aprovado em 30/10)

---

## 1. Objetivo & Escopo

O Estatuto da CBN-GO passa a ser publicado como página navegável gerada do XML **Akoma Ntoso 3.0** cadastrado no painel (tabela `statutes`). O XML é a fonte da verdade; o HTML é derivado dele.

### 1.1 Goals
- `GET /estatuto` (`estatuto.show`): versão publicada vigente, com cabeçalho (título, aprovação, vigência, ata, registro), sumário fixo/recolhível com destaque da parte visível, corpo completo e link permanente por artigo.
- `GET /estatuto.akn.xml` (`estatuto.xml`): o `akn_xml` do banco, byte a byte, com `Content-Type: application/akn+xml`.
- `GET /estatuto/versoes` e `GET /estatuto/versoes/{data}`: histórico (publicada + arquivadas) e leitura de uma versão com aviso "Versão não vigente".
- SEO: JSON-LD `schema.org/Legislation` + breadcrumbs; `/estatuto` no sitemap quando houver versão publicada.
- Quem Somos: o documento da categoria `estatuto` aponta para `/estatuto`.
- Cache do HTML renderizado, com chave versionada pelo `updated_at`.

### 1.2 Non-Goals
- Edição, validação XSD e publicação (painel).
- **PDF registrado:** não é publicado no site (contém assinaturas, carimbos e CPF). A página orienta a solicitá-lo à secretaria.

---

## 2. Contratos

### 2.1 Tabela `statutes` (espelho do painel)
`title`, `version_label` (única), `approved_at`, `approved_place`, `approval_body`, `minutes_reference`, `registry_info`, `effective_at`, `status` (`draft`, `published`, `archived`), `akn_xml`, `original_pdf`, `created_by`, `updated_by`, `published_by`, `published_at`. Só uma versão fica `published` por vez.

### 2.2 Renderização (`App\Support\Akn\StatuteRenderer`)
- `DOMDocument` + `DOMXPath` no namespace `http://docs.oasis-open.org/legaldocml/ns/akn/3.0`, com `LIBXML_NONET` (sem rede e sem entidades externas).
- Todo texto do XML é escapado; nenhuma marcação do XML é copiada para o HTML.
- Mapeamento: `chapter` → `<section id=eId>` + `<h2>`; `section` → `<section id=eId>` + `<h3>`; `article` → `<article id=eId>` + `<h4>` com link permanente; `paragraph` → `<div id=eId>`; `point` → `<ol>`/`<li id=eId>` (recursivo); `intro`/`content`/`wrapUp` → `<p>`.
- O `id` de cada elemento é o eId do AKN (ex.: `art_8__point_I__point_a`).
- Resultado (`RenderedStatute`): título, sumário (capítulos → seções), HTML, total de artigos, fecho e FRBR Work/Expression.
- XML malformado: exceção registrada no log e resposta 404.

---

## 3. Casos de borda
1. Nenhuma versão publicada → `/estatuto` e `/estatuto.akn.xml` respondem 404; `/estatuto` fica fora do sitemap e o card de Quem Somos mantém o link do arquivo.
2. Rascunhos nunca aparecem (nem em versões).
3. Texto com `<script>` ou HTML dentro do XML → exibido como texto.
4. Duas versões com a mesma data de aprovação → a mais recente (id) é exibida em `/estatuto/versoes/{data}`.

---

## 4. Critérios de aceite (TDD)
- 21 artigos renderizados com o XML da fixture; âncoras `chp_3__sec_2` e `art_8__point_I__point_a` presentes; sumário com 6 capítulos e 5 seções.
- XML baixado idêntico ao do banco, com o Content-Type correto.
- 404 sem versão publicada.
- Versões: lista e aviso de versão não vigente.
- Escape de HTML vindo do XML.
- Cache renovado quando `updated_at` muda.
- JSON-LD `Legislation` e sitemap; card de Quem Somos apontando para `/estatuto`.
