# Especificação Técnica (Spec) — Issue #9: Deploy do Portal Público (preview Hostinger)

- **Issue:** [#9 — [WDLC-5: DevOps] Deploy do Portal Público e Integração de CDN](https://github.com/cbn-go/portal-cbngo-site/issues/9)
- **Fase WDLC:** Fase 5 (Implantação & Operação)
- **Metodologia:** Spec-Driven Development (SDD) com ciclo TDD Mandatório
- **Data:** 26 de Outubro de 2026
- **Status:** Preview no ar (9.1 e 9.3). 9.2 (Cloudflare / domínio oficial) pendente.
- **URL de preview:** https://honeydew-wolverine-903854.hostingersite.com/ (substituída pelo domínio `cbngo.org.br` em 29/10/2026, ver seção 5)

---

## 1. Objetivo & Escopo

### 1.1 Contexto & Objetivo
O portal Laravel está pronto em `main`, mas ainda não é servido na Hostinger. Esta entrega publica o site no **preview** `honeydew-wolverine-903854.hostingersite.com` (LiteSpeed compartilhado), com cache de estáticos, compressão, HTTPS da Hostinger, publicação contínua a partir de `main` e monitoramento de `/health`.

O WordPress legado em `cbngo.com.br` permanece no ar. O preview `*.hostingersite.com` não é zona DNS nossa, então Cloudflare (sub-issue 9.2) e o DoD de domínio oficial ficam para o cutover.

### 1.2 Goals (Dentro do Escopo)

- **9.1 — Virtual host no preview (equivalente Hostinger/LiteSpeed):**
  - Document root = `public_html` do domínio honeydew, mapeado para o `public/` do Laravel.
  - Front controller via `.htaccess` (`RewriteRule` para `index.php`).
  - `Cache-Control` longo em js/css/woff2/png/webp/jpg/jpeg/gif/svg/ico/geojson.
  - HTML dinâmico **sem** full-page cache agressivo (notícias saem do painel).
  - Compressão Gzip e/ou Brotli no LiteSpeed (não na extensão PHP `brotli`).
  - HTTPS do `hostingersite.com` (certificado da Hostinger).
  - `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` do preview.
  - Deploy **nunca** executa `php artisan migrate`.
- **9.3 — Monitoramento:**
  - `GET /health` e `GET /up` respondem 200.
  - Checagem recorrente do preview (workflow agendado no GitHub Actions + instrução de UptimeRobot ou equivalente com alerta por e-mail).
- **Publicação contínua:**
  - Push em `main` dispara o job `deploy` depois do job `quality`.
  - Composer `--no-dev` e `npm run build` no Actions; rsync via SSH; no servidor só `config:cache`, `route:cache` e `view:cache`.
  - `.env` de produção é criado **uma vez** no servidor e nunca é sobrescrito pelo rsync.

### 1.3 Non-Goals (Fora de Escopo)

- **9.2 Cloudflare / DNS gerenciado / SSL universal Cloudflare.** `*.hostingersite.com` não é zona nossa. A issue #9 **não fecha** só com esta entrega.
- Substituir o WordPress em `cbngo.com.br`.
- Criar ou alterar a senha do MySQL do painel.
- Copiar `.env.example` para `.env` (nem no deploy, nem como passo automático no servidor).
- Instalar Uptime Kuma neste shared hosting ou no VPS PipeGrow.
- Usuário MySQL restrito a `SELECT` (a Hostinger expõe um usuário por banco, com permissões amplas). A garantia é operacional: o portal não roda `migrate`.

---

## 2. Contratos

### 2.1 Layout no servidor

Host SSH: alias `cbn-go` (`u579305537@82.180.153.107:65002`).

```
/home/u579305537/domains/honeydew-wolverine-903854.hostingersite.com/
  app/ bootstrap/ config/ vendor/ .env     # Laravel (fora da web)
  public_html/                             # = public/ do Laravel
    index.php  .htaccess  build/  images/  geo/
```

`public/index.php` já carrega `../vendor` e `../bootstrap`. O rsync envia o conteúdo de `public/` para `public_html/`.

### 2.2 Ambiente de produção (servidor)

| Chave | Valor |
|---|---|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://honeydew-wolverine-903854.hostingersite.com` |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | `127.0.0.1` (mesmo servidor; host público `srv816.hstgr.io`) |
| `DB_DATABASE` | `u579305537_cbngo` (dono: painel em `indigo-owl-926716.hostingersite.com`) |
| `SESSION_DRIVER` | `file` |
| `CACHE_STORE` | `file` |
| `QUEUE_CONNECTION` | `sync` |
| `MEDIA_URL` | URL pública do storage do painel (lida do `.env` do indigo/peachpuff; não inventar `midia.cbngo.org.br` nesta entrega) |

PHP CLI e FPM: 8.3. `exec` / `shell_exec` estão desabilitados; `proc_open` não. Composer e npm **não** rodam no servidor.

### 2.3 GitHub Secrets (job `deploy`)

- `HOSTINGER_SSH_KEY` — chave privada Ed25519 só de deploy
- `HOSTINGER_HOST` — `82.180.153.107`
- `HOSTINGER_PORT` — `65002`
- `HOSTINGER_USER` — `u579305537`

### 2.4 Monitoramento (9.3)

- Endpoint canônico: `GET https://honeydew-wolverine-903854.hostingersite.com/health` → `{"status":"ok"}`.
- Backup: `GET /up` (health nativo do Laravel).
- Workflow agendado `.github/workflows/uptime.yml` consulta `/health` a cada 15 minutos (monitor desta entrega).
- UptimeRobot (ou equivalente) pode apontar para o mesmo `/health`, com alerta por e-mail, como camada extra. URL do monitor externo: ainda não criada (conta UptimeRobot fora deste repositório).

---

## 3. Critérios de aceite

- [ ] `GET https://honeydew-wolverine-903854.hostingersite.com/` → 200, home Laravel (não a página padrão Hostinger).
- [ ] `/noticias`, `/igrejas` e `/artigos` reescrevem via `.htaccess`.
- [ ] HTTPS ativo; `APP_ENV=production` e `APP_DEBUG=false`.
- [ ] `/build/*`, `/images/*` e `/geo/*` com `Cache-Control` longo.
- [ ] HTML/CSS/JS com `content-encoding` gzip e/ou br.
- [ ] O workflow de deploy **não** contém `artisan migrate`.
- [ ] `GET /health` e `GET /up` → 200.
- [ ] Push em `main` publica depois do CI.
- [ ] WordPress `https://cbngo.com.br` inalterado.

---

## 4. Riscos / premissas

- O banco `u579305537_cbngo` já existe e é preenchido pelo painel. Se o painel ainda não tiver conteúdo publicado, a home abre vazia, não com 500.
- Cache LiteSpeed full-page da Hostinger, se ligado, pode servir HTML stale. Prefere-se desligado; o cache desta entrega é o `.htaccess` de estáticos.
- A issue #9 permanece aberta até 9.2 (Cloudflare + domínio oficial).

---

## 5. Atualização — domínio `cbngo.org.br` (29/10/2026)

O domínio `cbngo.org.br` foi conectado ao site no hPanel. A Hostinger renomeou a pasta do site de `/home/u579305537/domains/honeydew-wolverine-903854.hostingersite.com` para `/home/u579305537/domains/cbngo.org.br`.

**Incidente:** o `bootstrap/cache/config.php` (gerado por `config:cache` no deploy) guardava os caminhos absolutos da pasta antiga (`storage`, `vendor`, sessões, views compiladas). Depois da troca, todas as rotas, inclusive `/health` e `/up`, responderam 500, e o Laravel passou a gravar o log em `honeydew-…/storage/logs/laravel.log`.

**Correção no servidor:** `APP_URL=https://cbngo.org.br` no `.env`; remover `bootstrap/cache/config.php` e `bootstrap/cache/routes-v7.php`; `view:clear`; recriar `config:cache`, `route:cache` e `view:cache`.

**Correção no deploy (`.github/workflows/ci.yml`):**
- `REMOTE_ROOT` aponta para `/home/u579305537/domains/cbngo.org.br` e `SITE_URL` para `https://cbngo.org.br`.
- Antes de aquecer os caches, o deploy apaga os caches de configuração e rotas e roda `view:clear`, para nunca reaproveitar caminhos de uma pasta antiga.
- Depois de publicar, o job confere `GET ${SITE_URL}/health` e falha se o portal não responder.

**Monitoramento:** `.github/workflows/uptime.yml` passa a consultar `https://cbngo.org.br/health`.

O WordPress continua em `cbngo.com.br`. Cloudflare (9.2) segue pendente.
