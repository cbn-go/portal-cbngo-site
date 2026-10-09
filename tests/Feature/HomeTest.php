<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomeTest extends TestCase
{
    /**
     * Valida que o endpoint de healthcheck retorna HTTP 200 com JSON esperado.
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
     * Valida que a rota raiz (Home) responde HTTP 200 e exibe elementos institucionais da CBN-GO.
     */
    public function test_home_page_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Convenção Batista Nacional do Estado de Goiás');
        $response->assertSee('CBN-GO');
    }
}
