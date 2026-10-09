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
