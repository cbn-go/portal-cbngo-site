<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomeTest extends TestCase
{
    /**
     * Valida que o endpoint nativo /up do Laravel responde com HTTP 200.
     */
    public function test_native_up_endpoint_returns_ok_status(): void
    {
        $response = $this->get('/up');

        $response->assertStatus(200);
    }

    /**
     * Valida que o endpoint de healthcheck /health retorna HTTP 200 com JSON esperado.
     */
    public function test_healthcheck_endpoint_returns_ok_status(): void
    {
        $response = $this->get('/health');

        $response->assertStatus(200);
        $response->assertExactJson([
            'status' => 'ok',
        ]);
    }

    /**
     * Valida que a rota raiz (Home) responde HTTP 200 e exibe elementos institucionais da CBN-GO,
     * desacoplada da existência prévia do manifest do Vite via withoutVite().
     */
    public function test_home_page_renders_successfully(): void
    {
        $this->withoutVite();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Convenção Batista Nacional do Estado de Goiás');
        $response->assertSee('CBN-GO');
    }
}
