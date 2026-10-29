<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class DeployConfigTest extends TestCase
{
    /**
     * Valida que o front controller em public/.htaccess reescreve rotas e cacheia estáticos.
     */
    public function test_public_htaccess_rewrites_and_caches_static_assets(): void
    {
        $htaccess = file_get_contents(dirname(__DIR__, 2).'/public/.htaccess');

        $this->assertNotFalse($htaccess);
        $this->assertStringContainsString('RewriteRule', $htaccess);
        $this->assertStringContainsString('index.php', $htaccess);
        $this->assertStringContainsString('Cache-Control', $htaccess);
        $this->assertStringContainsString('js', $htaccess);
        $this->assertStringContainsString('css', $htaccess);
        $this->assertStringContainsString('woff2', $htaccess);
        $this->assertStringContainsString('png', $htaccess);
        $this->assertStringContainsString('webp', $htaccess);
        $this->assertStringContainsString('geojson', $htaccess);
    }

    /**
     * Valida que o job de deploy não roda migrate e só aquece caches do Artisan.
     */
    public function test_deploy_workflow_does_not_run_migrate(): void
    {
        $yaml = file_get_contents(dirname(__DIR__, 2).'/.github/workflows/ci.yml');

        $this->assertNotFalse($yaml);
        $this->assertMatchesRegularExpression('/^  deploy:\s*$/m', $yaml);
        $this->assertStringContainsString('config:cache', $yaml);
        $this->assertStringContainsString('route:cache', $yaml);
        $this->assertStringContainsString('view:cache', $yaml);
        $this->assertStringNotContainsString('artisan migrate', $yaml);
        $this->assertStringNotContainsString('php artisan migrate', $yaml);
    }

    /**
     * Valida que o deploy publica na pasta do domínio oficial, descarta caches com caminhos antigos
     * e confere o /health depois de publicar.
     */
    public function test_deploy_targets_official_domain_and_checks_health(): void
    {
        $yaml = (string) file_get_contents(dirname(__DIR__, 2).'/.github/workflows/ci.yml');

        $this->assertStringContainsString('REMOTE_ROOT: /home/u579305537/domains/cbngo.org.br', $yaml);
        $this->assertStringContainsString('SITE_URL: https://cbngo.org.br', $yaml);
        $this->assertStringContainsString('rm -f bootstrap/cache/config.php', $yaml);
        $this->assertStringContainsString('${SITE_URL}/health', $yaml);
        $this->assertStringNotContainsString('honeydew-wolverine', $yaml);
    }

    /**
     * Valida que o monitoramento periódico consulta o /health do domínio oficial do portal.
     */
    public function test_uptime_workflow_checks_portal_health_endpoint(): void
    {
        $yaml = file_get_contents(dirname(__DIR__, 2).'/.github/workflows/uptime.yml');

        $this->assertNotFalse($yaml);
        $this->assertStringContainsString('https://cbngo.org.br/health', $yaml);
        $this->assertStringContainsString('cron:', $yaml);
    }
}
