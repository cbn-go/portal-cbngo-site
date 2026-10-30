<?php

namespace Tests\Feature;

use App\Models\Church;
use App\Models\News;
use App\Models\SocialProject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChurchPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    /**
     * A página da igreja mostra nome, cidade, pastor, endereço, rotas, horários de culto e contatos.
     */
    public function test_church_page_shows_details_routes_service_times_and_contacts(): void
    {
        $church = $this->church();

        $response = $this->get('/igrejas/ibn-central-anapolis');
        $html = (string) $response->getContent();

        $response->assertOk();
        $this->assertMatchesRegularExpression('#<h1[^>]*>\s*Igreja Batista Nacional Central\s*</h1>#', $html);
        $this->assertSame(1, substr_count($html, '<h1'));
        $response->assertSee('Anápolis');
        $response->assertSee('Pr. João Batista');
        $response->assertSee('Rua Barão do Rio Branco, 120 – Sala 2');
        $response->assertSee('Setor Central');
        $response->assertSee('CEP 75020-000');
        $response->assertSee(e($church->getGoogleMapsUrl()), false);
        $response->assertSee(e($church->getWazeUrl()), false);
        $response->assertSeeInOrder(['Culto de Celebração', 'Domingo', '19h00']);
        $response->assertSeeInOrder(['Reunião de Oração', 'Quarta-feira', '20h00']);
        $response->assertSee('href="tel:+556233210000"', false);
        $response->assertSee('(62) 99999-1234');
        $response->assertSee('href="mailto:contato@ibncentral.org.br"', false);
        $response->assertSee('href="https://instagram.com/ibncentral"', false);
        $response->assertSee('href="'.e(route('igrejas', ['cidade' => 'Anápolis'])).'"', false);
    }

    /**
     * Title, description e og:image próprios da igreja.
     */
    public function test_church_page_has_local_seo_meta(): void
    {
        $this->church(['logo' => 'churches/logos/central.png']);

        $response = $this->get('/igrejas/ibn-central-anapolis');

        $response->assertSee('<title>Igreja Batista Nacional Central em Anápolis — CBN Goiás</title>', false);
        $response->assertSee('<meta name="description" content="Igreja Batista Nacional Central em Anápolis-GO. Rua Barão do Rio Branco, 120 – Sala 2, Setor Central. Cultos: Domingo 19h00, Quarta-feira 20h00.">', false);
        $response->assertSee('<link rel="canonical" href="'.route('igrejas.show', 'ibn-central-anapolis').'">', false);
        $response->assertSee('<meta property="og:image" content="https://midia.test/churches/logos/central-cover.webp">', false);
    }

    /**
     * Quando o nome já traz a cidade, title e description não a repetem.
     */
    public function test_seo_meta_does_not_repeat_city_already_in_name(): void
    {
        $this->church(['name' => 'Igreja Batista Nacional de Anápolis', 'address' => null, 'number' => null, 'complement' => null, 'neighborhood' => null]);

        $this->get('/igrejas/ibn-central-anapolis')
            ->assertSee('<title>Igreja Batista Nacional de Anápolis — CBN Goiás</title>', false)
            ->assertSee('<meta name="description" content="Igreja Batista Nacional de Anápolis, Anápolis-GO. Cultos: Domingo 19h00, Quarta-feira 20h00.">', false);
    }

    /**
     * Igreja inativa e slug inexistente respondem 404.
     */
    public function test_inactive_or_missing_church_returns_404(): void
    {
        $this->church(['is_active' => false]);

        $this->get('/igrejas/ibn-central-anapolis')->assertNotFound();
        $this->get('/igrejas/nao-existe')->assertNotFound();
    }

    /**
     * JSON-LD Church com endereço, geo, contatos, redes e a convenção como organização-mãe, mais o breadcrumb.
     */
    public function test_church_page_renders_church_structured_data(): void
    {
        $this->church();

        $data = $this->structuredData((string) $this->get('/igrejas/ibn-central-anapolis')->getContent());
        $church = collect($data)->firstWhere('@type', 'Church');

        $this->assertNotNull($church);
        $this->assertSame('Igreja Batista Nacional Central', $church['name']);
        $this->assertSame(route('igrejas.show', 'ibn-central-anapolis'), $church['url']);
        $this->assertSame([
            '@type' => 'PostalAddress',
            'streetAddress' => 'Rua Barão do Rio Branco, 120 – Sala 2 - Setor Central',
            'addressLocality' => 'Anápolis',
            'addressRegion' => 'GO',
            'postalCode' => '75020-000',
            'addressCountry' => 'BR',
        ], $church['address']);
        $this->assertSame(['@type' => 'GeoCoordinates', 'latitude' => -16.3285, 'longitude' => -48.9534], $church['geo']);
        $this->assertSame('(62) 3321-0000', $church['telephone']);
        $this->assertSame('contato@ibncentral.org.br', $church['email']);
        $this->assertSame(['https://instagram.com/ibncentral'], $church['sameAs']);
        $this->assertSame(url('/').'#organizacao', $church['parentOrganization']['@id']);
        $this->assertArrayNotHasKey('openingHoursSpecification', $church);

        $breadcrumb = collect($data)->firstWhere('@type', 'BreadcrumbList');
        $this->assertSame(['Início', 'Igrejas', 'Igreja Batista Nacional Central'], array_column($breadcrumb['itemListElement'], 'name'));
    }

    /**
     * Sem coordenadas válidas, o JSON-LD não tem geo; e-mail e redes inválidos ficam de fora da página e do JSON-LD.
     */
    public function test_invalid_coordinates_email_and_social_links_are_omitted(): void
    {
        $this->church([
            'latitude' => 10.0,
            'longitude' => 10.0,
            'email' => 'nao-e-email',
            'social_links' => ['instagram' => 'javascript:alert(1)'],
        ]);

        $response = $this->get('/igrejas/ibn-central-anapolis');
        $church = collect($this->structuredData((string) $response->getContent()))->firstWhere('@type', 'Church');

        $this->assertArrayNotHasKey('geo', $church);
        $this->assertArrayNotHasKey('email', $church);
        $this->assertArrayNotHasKey('sameAs', $church);
        $response->assertDontSee('mailto:nao-e-email', false);
        $response->assertDontSee('javascript:alert(1)', false);
    }

    /**
     * A página lista só os projetos e notícias publicados da própria igreja.
     */
    public function test_church_page_lists_only_its_published_projects_and_news(): void
    {
        $church = $this->church();
        $other = Church::factory()->create();

        SocialProject::factory()->forChurch($church)->create(['title' => 'Sopão da Central']);
        SocialProject::factory()->forChurch($church)->draft()->create(['title' => 'Projeto em Rascunho']);
        SocialProject::factory()->forChurch($other)->create(['title' => 'Projeto de Outra Igreja']);
        News::factory()->forChurch($church)->create(['title' => 'Central celebra 30 anos', 'published_at' => now()->subDay()]);
        News::factory()->forChurch($church)->draft()->create(['title' => 'Notícia em Rascunho']);
        News::factory()->forChurch($other)->create(['title' => 'Notícia de Outra Igreja', 'published_at' => now()->subDay()]);

        $response = $this->get('/igrejas/ibn-central-anapolis');

        $response->assertSee('Sopão da Central');
        $response->assertSee('Central celebra 30 anos');
        $response->assertDontSee('Projeto em Rascunho');
        $response->assertDontSee('Projeto de Outra Igreja');
        $response->assertDontSee('Notícia em Rascunho');
        $response->assertDontSee('Notícia de Outra Igreja');
    }

    /**
     * Sem projetos nem notícias, as seções não aparecem.
     */
    public function test_church_page_hides_empty_sections(): void
    {
        $this->church();

        $this->get('/igrejas/ibn-central-anapolis')
            ->assertOk()
            ->assertDontSee('Projetos sociais desta igreja')
            ->assertDontSee('Notícias desta igreja');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function church(array $attributes = []): Church
    {
        return Church::factory()->create(array_merge([
            'name' => 'Igreja Batista Nacional Central',
            'slug' => 'ibn-central-anapolis',
            'pastor_name' => 'Pr. João Batista',
            'address' => 'Rua Barão do Rio Branco',
            'number' => '120',
            'complement' => 'Sala 2',
            'neighborhood' => 'Setor Central',
            'city' => 'Anápolis',
            'state' => 'GO',
            'zip_code' => '75020-000',
            'latitude' => -16.3285,
            'longitude' => -48.9534,
            'phone' => '(62) 3321-0000',
            'cellphone' => '(62) 99999-1234',
            'email' => 'contato@ibncentral.org.br',
            'social_links' => ['instagram' => 'https://instagram.com/ibncentral'],
            'service_times' => [
                ['day' => 'Domingo', 'time' => '19h00', 'label' => 'Culto de Celebração'],
                ['day' => 'Quarta-feira', 'time' => '20h00', 'label' => 'Reunião de Oração'],
            ],
        ], $attributes));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function structuredData(string $html): array
    {
        preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $matches);

        return array_map(fn (string $json) => json_decode($json, true, 512, JSON_THROW_ON_ERROR), $matches[1]);
    }
}
