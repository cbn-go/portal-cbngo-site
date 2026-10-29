# Portal Institucional CBN-GO (Frontend)

Portal público institucional da **Convenção Batista Nacional do Estado de Goiás (CBN-GO)**.

## Tecnologias

- **PHP 8.2+ / Laravel 11.x**
- **Blade Templating**
- **Tailwind CSS 3.x**
- **Vite 6.x**
- **Laravel Pint** (Code Style & Linting)
- **Larastan / PHPStan** (Análise Estática Nível 5)
- **PHPUnit 11** (Testes Automatizados com TDD)
- **GitHub Actions** (CI/CD)

## Requisitos

- PHP >= 8.2 (com extensões `pdo_sqlite`, `mbstring`, `xml`, `intl`, `iconv`, `ctype`)
- Composer >= 2.x
- Node.js >= 20.x e npm >= 10.x

## Instalação e Execução

1. Clone o repositório:
   ```bash
   git clone git@github.com:cbn-go/portal-cbngo-site.git
   cd portal-cbngo-site
   ```

2. Instale as dependências:
   ```bash
   composer install
   npm install
   ```

3. Configure o arquivo de ambiente:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   O portal lê o **banco MySQL compartilhado com o painel administrativo** ([cbn-go/portal-cbngo-admin](https://github.com/cbn-go/portal-cbngo-admin)), que é o dono do schema. O portal só lê (em produção, com um usuário que tem apenas `SELECT`) e **o deploy nunca executa `php artisan migrate`**. As imagens vêm do storage de mídia do painel (`MEDIA_URL`).

   Para desenvolver, há duas opções:
   - Apontar `DB_*` para o MySQL local do painel, depois de rodar `php artisan migrate:fresh --seed` lá.
   - Usar SQLite sem o painel: defina `DB_CONNECTION=sqlite`, comente as demais linhas `DB_*` e rode `php artisan migrate --seed`. Esse comando carrega o espelho do schema do painel em `database/migrations/testing`, que é o mesmo usado pelos testes.

4. Compile os assets do frontend:
   ```bash
   npm run build
   ```

5. Inicie o servidor de desenvolvimento:
   ```bash
   composer run dev
   # ou separadamente:
   # php artisan serve
   # npm run dev
   ```

## Comandos de Qualidade e Testes

- **Executar Testes:**
  ```bash
  composer test
  ```

- **Verificar Estilo de Código (Lint):**
  ```bash
  composer lint
  ```

- **Corrigir Estilo de Código Automaticamente:**
  ```bash
  composer lint:fix
  ```

- **Análise Estática (PHPStan Nível 5):**
  ```bash
  composer phpstan
  ```

## Publicação (Hostinger)

O portal vive em https://cbngo.org.br/ (pasta `/home/u579305537/domains/cbngo.org.br` no servidor; antes era o preview `honeydew-wolverine-903854.hostingersite.com`). A spec está em [`specs/deploy-portal.md`](specs/deploy-portal.md).

- Se o domínio ou a pasta do site mudar no hPanel, atualize `REMOTE_ROOT`, `SITE_URL` (`.github/workflows/ci.yml`), o monitor (`.github/workflows/uptime.yml`) e o `APP_URL` do `.env` no servidor, e apague `bootstrap/cache/config.php`: o cache de configuração guarda caminhos absolutos da pasta antiga e derruba o site com erro 500.

- Push em `main` publica depois do job `quality` (rsync via SSH). O deploy **não** executa `php artisan migrate`.
- O `.env` de produção é criado **uma vez** no servidor (`APP_ENV=production`, `APP_DEBUG=false`) e o rsync o ignora. Não copie `.env.example` no servidor.
- Secrets do repositório (Actions):
  - `HOSTINGER_SSH_KEY` — chave privada Ed25519 só de deploy
  - `HOSTINGER_HOST` — `82.180.153.107`
  - `HOSTINGER_PORT` — `65002`
  - `HOSTINGER_USER` — `u579305537`
- Monitoramento: `.github/workflows/uptime.yml` consulta `/health` a cada 15 minutos. Configure também um check no UptimeRobot (ou equivalente) para o mesmo endpoint, com alerta por e-mail.
- Cloudflare e o domínio oficial (`cbngo.com.br`) **não** entram nesta entrega; o WordPress legado permanece no ar.
