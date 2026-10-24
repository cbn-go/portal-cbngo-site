<?php

namespace Tests\Feature;

use App\Models\Church;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChurchDirectoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Valida que a página /igrejas exibe as congregações ativas e suporta paginação de 9 itens por página (AC 6.1).
     */
    public function test_churches_index_displays_active_churches_and_pagination(): void
    {
        $this->withoutVite();

        for ($i = 1; $i <= 12; $i++) {
            $num = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            Church::create([
                'name' => "Igreja Batista Nacional {$num}",
                'slug' => "igreja-batista-nacional-{$num}",
                'pastor_name' => "Pr. Titular {$num}",
                'address' => "Av. Goiás, {$num}00",
                'neighborhood' => 'Setor Central',
                'city' => 'Goiânia',
                'state' => 'GO',
                'latitude' => -16.6799,
                'longitude' => -49.2550,
                'is_active' => true,
            ]);
        }

        // Página 1: deve exibir os 9 primeiros cards em ordem alfabética (01 até 09), mantendo todas as 12 contabilizadas no mapa municipal
        $response = $this->get(route('igrejas'));

        $response->assertStatus(200);
        $response->assertSee('id="igreja-igreja-batista-nacional-01"', false);
        $response->assertSee('id="igreja-igreja-batista-nacional-09"', false);
        $response->assertDontSee('id="igreja-igreja-batista-nacional-10"', false);
        $response->assertSee('data-mapa="municipios"', false);
        $response->assertViewHas('presenceByCity', ['Goiânia' => 12]);
        $response->assertDontSee('data-church-marker=', false);
        $response->assertSee('page=2', false);

        // Página 2: deve exibir os 3 cards restantes (10 até 12) e ocultar o card 01 da página anterior
        $responsePage2 = $this->get(route('igrejas', ['page' => 2]));

        $responsePage2->assertStatus(200);
        $responsePage2->assertSee('id="igreja-igreja-batista-nacional-10"', false);
        $responsePage2->assertSee('id="igreja-igreja-batista-nacional-12"', false);
        $responsePage2->assertDontSee('id="igreja-igreja-batista-nacional-01"', false);
        $responsePage2->assertViewHas('presenceByCity', ['Goiânia' => 12]);
    }

    /**
     * Valida que congregações inativas (is_active = false) são ocultadas da listagem, do mapa e do dropdown de cidades.
     */
    public function test_churches_index_hides_inactive_churches(): void
    {
        $this->withoutVite();

        Church::create([
            'name' => 'IBN Ativa em Goiânia',
            'slug' => 'ibn-ativa-goiania',
            'pastor_name' => 'Pr. Marcos Vinícius',
            'address' => 'Rua T-30, 500',
            'neighborhood' => 'Setor Bueno',
            'city' => 'Goiânia',
            'state' => 'GO',
            'latitude' => -16.7069,
            'longitude' => -49.2700,
            'is_active' => true,
        ]);

        Church::create([
            'name' => 'IBN Inativa em Catalão',
            'slug' => 'ibn-inativa-catalao',
            'pastor_name' => 'Pr. Paulo Henrique',
            'address' => 'Rua Central, 100',
            'neighborhood' => 'Centro',
            'city' => 'Catalão',
            'state' => 'GO',
            'latitude' => -18.1661,
            'longitude' => -47.9447,
            'is_active' => false,
        ]);

        $response = $this->get(route('igrejas'));

        $response->assertStatus(200);
        $response->assertSee('IBN Ativa em Goiânia');
        $response->assertDontSee('IBN Inativa em Catalão');
        $response->assertDontSee('Catalão');
        $response->assertViewHas('presenceByCity', ['Goiânia' => 1]);
    }

    /**
     * Valida que o campo de busca rápida filtra por nome da igreja ou por bairro/setor (AC 6.1).
     */
    public function test_churches_index_can_search_by_church_name_or_neighborhood(): void
    {
        $this->withoutVite();

        Church::create([
            'name' => 'Igreja Batista Nacional Betel',
            'slug' => 'ibn-betel',
            'pastor_name' => 'Pr. Roberto Silveira',
            'address' => 'Av. T-63, 1200',
            'neighborhood' => 'Setor Bueno',
            'city' => 'Goiânia',
            'state' => 'GO',
            'is_active' => true,
        ]);

        Church::create([
            'name' => 'Igreja Batista Nacional Ebenézer',
            'slug' => 'ibn-ebenezer',
            'pastor_name' => 'Pr. Lucas Almeida',
            'address' => 'Rua Manoel D\'Abadia, 340',
            'neighborhood' => 'Bairro Jundiaí',
            'city' => 'Anápolis',
            'state' => 'GO',
            'is_active' => true,
        ]);

        // 1. Busca pelo nome da igreja
        $responseByName = $this->get(route('igrejas', ['q' => 'Betel']));
        $responseByName->assertStatus(200);
        $responseByName->assertSee('Igreja Batista Nacional Betel');
        $responseByName->assertDontSee('Igreja Batista Nacional Ebenézer');

        // 2. Busca pelo bairro/setor
        $responseByNeighborhood = $this->get(route('igrejas', ['q' => 'Jundiaí']));
        $responseByNeighborhood->assertStatus(200);
        $responseByNeighborhood->assertSee('Igreja Batista Nacional Ebenézer');
        $responseByNeighborhood->assertDontSee('Igreja Batista Nacional Betel');
    }

    /**
     * Valida que o dropdown de cidades lista todas as cidades goianas ativas e filtra as congregações pelo município (AC 6.1).
     */
    public function test_churches_index_can_filter_by_city_dropdown(): void
    {
        $this->withoutVite();

        Church::create([
            'name' => 'Primeira IBN de Goiânia',
            'slug' => 'primeira-ibn-goiania',
            'pastor_name' => 'Pr. Roberto Silveira',
            'address' => 'Rua 85, 410',
            'neighborhood' => 'Setor Sul',
            'city' => 'Goiânia',
            'state' => 'GO',
            'is_active' => true,
        ]);

        Church::create([
            'name' => 'IBN Central de Anápolis',
            'slug' => 'ibn-central-anapolis',
            'pastor_name' => 'Pr. André Moura',
            'address' => 'Av. Brasil Norte, 800',
            'neighborhood' => 'Centro',
            'city' => 'Anápolis',
            'state' => 'GO',
            'is_active' => true,
        ]);

        Church::create([
            'name' => 'IBN Aliança de Rio Verde',
            'slug' => 'ibn-alianca-rio-verde',
            'pastor_name' => 'Pr. Samuel Costa',
            'address' => 'Av. Presidente Vargas, 1500',
            'neighborhood' => 'Jardim Goiás',
            'city' => 'Rio Verde',
            'state' => 'GO',
            'is_active' => true,
        ]);

        $response = $this->get(route('igrejas', ['cidade' => 'Anápolis']));

        $response->assertStatus(200);
        // Exibe apenas o card da cidade filtrada
        $response->assertSee('IBN Central de Anápolis');
        $response->assertDontSee('Primeira IBN de Goiânia');
        $response->assertDontSee('IBN Aliança de Rio Verde');

        // O dropdown continua oferecendo todas as cidades goianas cadastradas
        $response->assertSee('<option value="Anápolis" selected>', false);
        $response->assertSee('<option value="Goiânia"', false);
        $response->assertSee('<option value="Rio Verde"', false);
    }

    /**
     * Valida que a paginação preserva os parâmetros de busca (q) e filtro de município (cidade).
     */
    public function test_churches_pagination_preserves_search_and_city_query_parameters(): void
    {
        $this->withoutVite();

        for ($i = 1; $i <= 11; $i++) {
            $num = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            Church::create([
                'name' => "IBN Filadélfia {$num}",
                'slug' => "ibn-filadelfia-{$num}",
                'pastor_name' => "Pr. Obreiro {$num}",
                'address' => "Rua {$num}, 100",
                'neighborhood' => 'Setor Oeste',
                'city' => 'Goiânia',
                'state' => 'GO',
                'is_active' => true,
            ]);
        }

        $response = $this->get(route('igrejas', ['q' => 'Filadélfia', 'cidade' => 'Goiânia']));

        $response->assertStatus(200);
        $response->assertSee('q=Filad%C3%A9lfia', false);
        $response->assertSee('cidade=Goi%C3%A2nia', false);
        $response->assertSee('page=2', false);
    }

    /**
     * Valida que um estado vazio amigável é exibido quando nenhuma congregação corresponder aos filtros.
     */
    public function test_churches_index_shows_empty_state_when_no_congregations_match(): void
    {
        $this->withoutVite();

        $response = $this->get(route('igrejas', ['q' => 'bairro_ou_igreja_inexistente_999']));

        $response->assertStatus(200);
        $response->assertSee('Nenhuma congregação encontrada');
        $response->assertSee('Limpar filtros');
        $response->assertViewHas('presenceByCity', []);
        $response->assertSee('0 / 246 municípios');
    }

    /**
     * Valida que os cards detalhados exibem todos os dados exigidos na sub-issue 6.2:
     * nome, logotipo, pastor titular, endereço completo, rotas Google Maps/Waze,
     * telefones, e-mail, redes sociais e horários regulares de cultos e oração.
     */
    public function test_church_card_renders_complete_details_routes_contacts_and_service_times(): void
    {
        $this->withoutVite();

        Church::create([
            'name' => 'Catedral Batista Nacional de Goiânia',
            'slug' => 'catedral-batista-nacional-goiania',
            'logo' => 'churches/logos/catedral-goiania.png',
            'pastor_name' => 'Pr. Roberto Silveira',
            'address' => 'Av. T-9',
            'number' => '2450',
            'complement' => 'Quadra 12',
            'neighborhood' => 'Jardim América',
            'city' => 'Goiânia',
            'state' => 'GO',
            'zip_code' => '74255-220',
            'latitude' => -16.7156000,
            'longitude' => -49.2834000,
            'phone' => '(62) 3251-4000',
            'cellphone' => '(62) 99988-7766',
            'email' => 'secretaria@catedralcbngo.org.br',
            'social_links' => [
                'instagram' => 'https://instagram.com/catedralcbngo',
                'facebook' => 'https://facebook.com/catedralcbngo',
                'youtube' => 'https://youtube.com/@catedralcbngo',
            ],
            'service_times' => [
                ['day' => 'Domingo', 'time' => '09h00', 'label' => 'Escola Bíblica Dominical'],
                ['day' => 'Domingo', 'time' => '19h00', 'label' => 'Culto de Celebração e Adoração'],
                ['day' => 'Quarta-feira', 'time' => '20h00', 'label' => 'Reunião de Oração e Estudo Bíblico'],
            ],
            'is_active' => true,
        ]);

        $response = $this->get(route('igrejas'));

        $response->assertStatus(200);
        // Nome e logotipo
        $response->assertSee('Catedral Batista Nacional de Goiânia');
        $response->assertSee('https://midia.test/churches/logos/catedral-goiania.png');
        // Pastor titular
        $response->assertSee('Pr. Roberto Silveira');
        // Endereço completo e links de rota Google Maps / Waze
        $response->assertSee('Av. T-9, 2450 – Quadra 12');
        $response->assertSee('Jardim América');
        $response->assertSee('74255-220');
        $response->assertSee('google.com/maps', false);
        $response->assertSee('waze.com/ul', false);
        // Contatos e redes sociais
        $response->assertSee('(62) 3251-4000');
        $response->assertSee('(62) 99988-7766');
        $response->assertSee('mailto:secretaria@catedralcbngo.org.br', false);
        $response->assertSee('https://instagram.com/catedralcbngo');
        $response->assertSee('https://facebook.com/catedralcbngo');
        $response->assertSee('https://youtube.com/@catedralcbngo');
        // Horários de cultos e reuniões de oração
        $response->assertSee('Escola Bíblica Dominical');
        $response->assertSee('09h00');
        $response->assertSee('Culto de Celebração e Adoração');
        $response->assertSee('19h00');
        $response->assertSee('Reunião de Oração e Estudo Bíblico');
        $response->assertSee('20h00');
    }

    /**
     * Valida que congregações sem logotipo cadastrado exibem um fallback institucional elegante (AC 6.2).
     */
    public function test_church_card_renders_fallback_when_logo_is_missing(): void
    {
        $this->withoutVite();

        Church::create([
            'name' => 'IBN Monte Sião',
            'slug' => 'ibn-monte-siao',
            'logo' => null,
            'pastor_name' => 'Pr. Ezequiel Tavares',
            'address' => 'Rua das Palmeiras, 120',
            'neighborhood' => 'Setor Central',
            'city' => 'Trindade',
            'state' => 'GO',
            'is_active' => true,
        ]);

        $response = $this->get(route('igrejas'));

        $response->assertStatus(200);
        $response->assertSee('IBN Monte Sião');
        $response->assertSee('data-testid="church-logo-fallback"', false);
    }

    /**
     * Valida que o Mapa Coroplético Municipal de Goiás é renderizado com container Leaflet,
     * asset GeoJSON dos 246 municípios do IBGE, legenda com/sem presença, contadores e payload presenceByCity,
     * sem pinos individuais (AC 6.3).
     */
    public function test_goias_map_renders_choropleth_municipalities_and_legend(): void
    {
        $this->withoutVite();

        Church::create([
            'name' => 'IBN Sede Goiânia',
            'slug' => 'ibn-sede-goiania',
            'pastor_name' => 'Pr. Roberto Silveira',
            'address' => 'Av. Anhanguera, 3000',
            'neighborhood' => 'Setor Aeroporto',
            'city' => 'Goiânia',
            'state' => 'GO',
            'latitude' => -16.6799000,
            'longitude' => -49.2550000,
            'is_active' => true,
        ]);

        Church::create([
            'name' => 'IBN Betel Goiânia',
            'slug' => 'ibn-betel-goiania',
            'pastor_name' => 'Pr. Marcos Vinícius',
            'address' => 'Rua T-37, 1120',
            'neighborhood' => 'Setor Bueno',
            'city' => 'Goiânia',
            'state' => 'GO',
            'latitude' => -16.7032000,
            'longitude' => -49.2665000,
            'is_active' => true,
        ]);

        Church::create([
            'name' => 'IBN Anápolis',
            'slug' => 'ibn-anapolis',
            'pastor_name' => 'Pr. Carlos Eduardo',
            'address' => 'Av. Goiás, 900',
            'neighborhood' => 'Jundiaí',
            'city' => 'Anápolis',
            'state' => 'GO',
            'latitude' => -16.3285000,
            'longitude' => -48.9534000,
            'is_active' => true,
        ]);

        $response = $this->get(route('igrejas'));

        $response->assertStatus(200);
        $response->assertSee('id="mapa-goias"', false);
        $response->assertSee('data-mapa="municipios"', false);
        $response->assertSee('id="leaflet-mapa-goias"', false);
        $response->assertSee('/geo/goias-municipios.geojson', false);
        $response->assertSee('Com congregação filiada');
        $response->assertSee('Sem congregação filiada');
        $response->assertSee('2 / 246 municípios');
        $response->assertDontSee('data-church-marker', false);
        $response->assertViewHas('presenceByCity', [
            'Anápolis' => 1,
            'Goiânia' => 2,
        ]);

        // Verifica que o asset GeoJSON de municípios de Goiás existe e contém a malha municipal
        $geojsonPath = public_path('geo/goias-municipios.geojson');
        $this->assertFileExists($geojsonPath);
        $decoded = json_decode((string) file_get_contents($geojsonPath), true);
        $this->assertIsArray($decoded);
        $this->assertSame('FeatureCollection', $decoded['type'] ?? null);
        $this->assertCount(246, $decoded['features'] ?? []);
    }

    /**
     * Valida que os filtros de busca e cidade sincronizam com presenceByCity no Mapa Municipal de Goiás (AC 6.3).
     */
    public function test_goias_map_synchronizes_presence_by_city_with_search_and_city_filters(): void
    {
        $this->withoutVite();

        Church::create([
            'name' => 'IBN Goiânia Sul',
            'slug' => 'ibn-goiania-sul',
            'pastor_name' => 'Pr. Roberto Silveira',
            'address' => 'Rua 90, 100',
            'neighborhood' => 'Setor Sul',
            'city' => 'Goiânia',
            'state' => 'GO',
            'latitude' => -16.6920000,
            'longitude' => -49.2540000,
            'is_active' => true,
        ]);

        Church::create([
            'name' => 'IBN Rio Verde Sudoeste',
            'slug' => 'ibn-rio-verde-sudoeste',
            'pastor_name' => 'Pr. Samuel Costa',
            'address' => 'Av. Presidente Vargas, 1200',
            'neighborhood' => 'Centro',
            'city' => 'Rio Verde',
            'state' => 'GO',
            'latitude' => -17.7923000,
            'longitude' => -50.9192000,
            'is_active' => true,
        ]);

        $response = $this->get(route('igrejas', ['cidade' => 'Rio Verde']));

        $response->assertStatus(200);
        $response->assertViewHas('presenceByCity', ['Rio Verde' => 1]);
        $response->assertSee('1 / 246 municípios');
        $response->assertDontSee('data-church-marker', false);
    }

    /**
     * Valida que congregações sem coordenadas válidas (nulas ou fora do estado/globo)
     * continuam contabilizadas na presença municipal e aparecem normalmente na listagem com rotas de texto (AC 6.3).
     */
    public function test_churches_without_valid_coordinates_do_not_break_map_and_appear_in_list(): void
    {
        $this->withoutVite();

        Church::create([
            'name' => 'IBN Sem Coordenadas Cadastradas',
            'slug' => 'ibn-sem-coordenadas',
            'pastor_name' => 'Pr. João Batista',
            'address' => 'Rua Couto Magalhães, 45',
            'neighborhood' => 'Centro',
            'city' => 'Pirenópolis',
            'state' => 'GO',
            'latitude' => null,
            'longitude' => null,
            'is_active' => true,
        ]);

        Church::create([
            'name' => 'IBN Coordenada Fora da Faixa',
            'slug' => 'ibn-coordenada-fora',
            'pastor_name' => 'Pr. Pedro Lucas',
            'address' => 'Av. Brasil, 200',
            'neighborhood' => 'Setor Central',
            'city' => 'Formosa',
            'state' => 'GO',
            'latitude' => -45.0000000,
            'longitude' => -120.0000000,
            'is_active' => true,
        ]);

        $response = $this->get(route('igrejas'));

        $response->assertStatus(200);
        // Ambas aparecem na listagem de cards com seus IDs e links de rotas por endereço textual
        $response->assertSee('IBN Sem Coordenadas Cadastradas');
        $response->assertSee('id="igreja-ibn-sem-coordenadas"', false);
        $response->assertSee('IBN Coordenada Fora da Faixa');
        $response->assertSee('id="igreja-ibn-coordenada-fora"', false);
        $response->assertSee('google.com/maps', false);
        $response->assertSee('waze.com/ul', false);

        // Ambas contam para a presença municipal por cidade no mapa coroplético
        $response->assertViewHas('presenceByCity', [
            'Formosa' => 1,
            'Pirenópolis' => 1,
        ]);
        $response->assertSee('2 / 246 municípios');
        $response->assertDontSee('data-church-marker', false);
    }

    /**
     * Valida que esquemas maliciosos em social_links (ex: javascript: ou data:) são descartados (Segurança / XSS).
     */
    public function test_church_social_links_filter_disallowed_schemes_preventing_xss(): void
    {
        $this->withoutVite();

        Church::create([
            'name' => 'IBN Segurança Digital',
            'slug' => 'ibn-seguranca-digital',
            'pastor_name' => 'Pr. Daniel Pereira',
            'address' => 'Rua 10, 50',
            'neighborhood' => 'Setor Oeste',
            'city' => 'Goiânia',
            'state' => 'GO',
            'email' => 'email-invalido"><script>alert(1)</script>',
            'social_links' => [
                'instagram' => 'https://instagram.com/ibnseguranca',
                'xss' => 'javascript:alert(document.cookie)',
                'data' => 'data:text/html,<script>alert(1)</script>',
            ],
            'is_active' => true,
        ]);

        $response = $this->get(route('igrejas'));

        $response->assertStatus(200);
        $response->assertSee('https://instagram.com/ibnseguranca');
        $response->assertDontSee('javascript:', false);
        $response->assertDontSee('data:text/html', false);
        $response->assertDontSee('mailto:email-invalido', false);
    }

    /**
     * Valida que o navbar destaca 'Igrejas' com aria-current="page" na página /igrejas.
     */
    public function test_navbar_highlights_igrejas_as_active_on_directory_page(): void
    {
        $this->withoutVite();

        $response = $this->get(route('igrejas'));

        $response->assertStatus(200);
        $response->assertSee('href="'.route('igrejas').'"', false);
        $response->assertSee('aria-current="page"', false);
    }

    /**
     * Valida que o DatabaseSeeder popula o banco com congregações representativas em Goiás.
     */
    public function test_database_seeder_populates_churches_in_goias(): void
    {
        $this->withoutVite();

        $this->seed();

        $this->assertGreaterThanOrEqual(6, Church::count());

        $response = $this->get(route('igrejas'));
        $response->assertStatus(200);
        $response->assertSee('Goiânia');
        $response->assertSee('Anápolis');
        $response->assertSee('Rio Verde');
    }
}
