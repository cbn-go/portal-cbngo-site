# Spec: Localização dos mockups (design)

## Contexto / problema

O portal CBN-GO usa mockups gerados no Google Stitch como referência visual da home institucional. Sem um mapa claro de onde ficam a fonte canônica e as cópias locais, a equipe (e agentes) arrisca implementar a partir de artefato desatualizado ou de logo inventada pelo Stitch.

## Objetivo

Documentar **onde ficam os mockups**, qual é a fonte da verdade e o que **não** deve ser tratado como asset de produção (especialmente logos).

## Escopo

- Localização do projeto Stitch (remoto)
- Cópias locais em `tmp/` (HTML e screenshots)
- Relação com assets oficiais de marca no repositório
- Critérios de aceite para uso correto dos mockups

## Fora de escopo

- Implementação Blade/Tailwind em si
- Versionamento obrigatório dos arquivos em `tmp/` no Git
- Design system completo (cores/tipografia — ver Manual da Marca e `tailwind.config.js`)

## Fonte da verdade (remoto)

| Item | Valor |
|------|--------|
| Ferramenta | [Google Stitch](https://stitch.withgoogle.com) |
| Projeto | `7571252793833866214` |
| URL do projeto | https://stitch.withgoogle.com/projects/7571252793833866214 |
| Tela de referência (home redesenhada) | `1ffd05c73b2644e2b6da94e49a1638d5` |
| Título da tela | CBN-GO — Portal Institucional Redesenhado |

A **referência visual canônica** é a tela no Stitch. Cópias locais são snapshots auxiliares.

## Cópias locais (repositório)

Diretório: `tmp/` (raiz do projeto)

| Arquivo | Tipo | Uso |
|---------|------|-----|
| `tmp/stitch-home-latest.html` | HTML exportado | Snapshot mais recente baixado do Stitch (preferir este entre os HTML locais) |
| `tmp/stitch-home-v2.html` | HTML | Versão intermediária do redesign |
| `tmp/stitch-home.html` | HTML | Export inicial |
| `tmp/stitch-cbn-home-v2.png` | Screenshot | Preview visual v2 |
| `tmp/stitch-cbn-home.png` | Screenshot | Preview visual inicial |
| `tmp/stitch-logo-header.png` | PNG | Recorte/logo gerada pelo Stitch — **não usar em produção** |

### Convenção

- Novos downloads do Stitch devem ir para `tmp/` com prefixo `stitch-`.
- Preferir sobrescrever ou criar `stitch-home-latest.html` / screenshot datado ao regenerar.
- `tmp/` é área de trabalho de design; não é pasta de assets públicos do Laravel (`public/`).

## Assets oficiais (não são mockups Stitch)

Logos e tipografia de produção **não** vêm do Stitch:

| Asset | Caminho |
|-------|---------|
| Logo clara (header) | `public/images/brand/cbn-go.png` (fonte: `resources/assets/images/brand/`) |
| Logo escura (footer) | `public/images/brand/cbn-go-on-dark.png` |
| Vetor / tipografia de marca | `resources/assets/vector/CBN_ESTADUAIS.ai` |
| Fontes web | `resources/assets/fonts/` (`baker-signet.ttf`, `humanist-521.ttf`) |

Ao portar mockup → Blade, **sempre** substituir imagens de logo do Stitch pelos PNGs oficiais acima.

## Critérios de aceite

- [ ] Existe `specs/mockups-localizacao.md` descrevendo projeto Stitch e pasta `tmp/`
- [ ] A URL do projeto Stitch e o ID da tela de home estão documentados
- [ ] Está explícito que `tmp/stitch-logo-header.png` e logos geradas pelo Stitch não vão para produção
- [ ] Está explícito que logos oficiais ficam em `public/images/brand/`
- [ ] Implementações novas da home consultam esta spec (ou o Stitch) antes de inventar layout

## Premissas e riscos

- Premissa: o projeto Stitch permanece acessível à equipe com a conta Google vinculada.
- Risco: HTML/PNG em `tmp/` podem ficar defasados em relação ao Stitch — em conflito, o Stitch vence.
- Risco: o Stitch regenera logos fictícias; nunca promover essas imagens para `public/images/brand/`.

## Referência rápida

```
Stitch (canônico)  →  https://stitch.withgoogle.com/projects/7571252793833866214
Snapshots locais   →  tmp/stitch-*
Logos produção     →  public/images/brand/cbn-go*.png
```
