<?php

namespace Tests\Unit;

use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Application;
use PHPUnit\Framework\TestCase;

class PublicPathTest extends TestCase
{
    private string $basePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->basePath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'cbngo-public-path-'.uniqid();
        mkdir($this->basePath);
    }

    protected function tearDown(): void
    {
        foreach ([$this->basePath.DIRECTORY_SEPARATOR.'public_html', $this->basePath] as $directory) {
            if (is_dir($directory)) {
                rmdir($directory);
            }
        }

        parent::tearDown();
    }

    /**
     * Na Hostinger (com public_html), o Laravel usa public_html como pasta pública: é lá que o deploy
     * publica o build do Vite, e o manifesto precisa ser lido do mesmo lugar.
     */
    public function test_uses_public_html_as_public_path_when_it_exists(): void
    {
        mkdir($this->basePath.DIRECTORY_SEPARATOR.'public_html');
        $app = new Application($this->basePath);

        (new AppServiceProvider($app))->register();

        $this->assertSame($this->basePath.DIRECTORY_SEPARATOR.'public_html', $app->publicPath());
    }

    /**
     * Sem public_html (desenvolvimento e CI), a pasta pública continua sendo public/.
     */
    public function test_keeps_default_public_path_without_public_html(): void
    {
        $app = new Application($this->basePath);

        (new AppServiceProvider($app))->register();

        $this->assertSame($this->basePath.DIRECTORY_SEPARATOR.'public', $app->publicPath());
    }
}
