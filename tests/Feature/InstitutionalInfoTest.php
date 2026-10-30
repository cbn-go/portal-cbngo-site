<?php

namespace Tests\Feature;

use App\Models\InstitutionalSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InstitutionalInfoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    /**
     * Com registro no painel, barra superior, rodapé e /contato mostram os dados do banco.
     */
    public function test_layout_and_contact_page_show_panel_data(): void
    {
        InstitutionalSetting::factory()->create([
            'email' => 'secretaria@cbngo.org.br',
            'phone' => '(62) 3212-0000',
            'whatsapp' => '(62) 99111-2222',
            'office_hours' => 'Segunda a sexta, das 8h às 17h',
            'maps_url' => 'https://maps.app.goo.gl/sedecbngo',
        ]);

        $home = $this->get(route('home'));
        preg_match('#<footer.*</footer>#s', (string) $home->getContent(), $footer);

        $home->assertSee('href="mailto:secretaria@cbngo.org.br"', false);
        $this->assertStringContainsString('Rua 04, 515, 12º andar, sala 1.207, Edifício Parthenon Center', $footer[0]);
        $this->assertStringContainsString('Setor Central, Goiânia - GO', $footer[0]);
        $this->assertStringContainsString('CEP 74020-045', $footer[0]);
        $this->assertStringContainsString('href="tel:+556232120000"', $footer[0]);
        $this->assertStringContainsString('href="https://wa.me/5562991112222"', $footer[0]);
        $this->assertStringContainsString('Segunda a sexta, das 8h às 17h', $footer[0]);
        $home->assertDontSee('contato@cbngo.org.br', false);

        $contact = $this->get(route('contato'));
        $contact->assertOk();
        $contact->assertSee('Rua 04, 515, 12º andar, sala 1.207, Edifício Parthenon Center');
        $contact->assertSee('href="https://maps.app.goo.gl/sedecbngo"', false);
        $contact->assertSee('href="tel:+556232120000"', false);
        $contact->assertSee('href="https://wa.me/5562991112222"', false);
        $contact->assertSee('Segunda a sexta, das 8h às 17h');
    }

    /**
     * Sem maps_url, "Como chegar" busca o endereço montado no Google Maps.
     */
    public function test_directions_link_falls_back_to_maps_search_with_full_address(): void
    {
        InstitutionalSetting::factory()->create(['maps_url' => null]);

        $address = 'Rua 04, 515, 12º andar, sala 1.207, Edifício Parthenon Center - Setor Central, Goiânia - GO, CEP 74020-045';

        $this->get(route('contato'))
            ->assertSee('https://www.google.com/maps/search/?api=1&amp;query='.rawurlencode($address), false);
    }

    /**
     * Sem registro no painel, o site usa o fallback de config/cbngo.php.
     */
    public function test_falls_back_to_config_without_panel_record(): void
    {
        config([
            'cbngo.contact.email' => 'fallback@cbngo.org.br',
            'cbngo.contact.phone' => '(62) 3000-1111',
            'cbngo.contact.street' => 'Rua do Fallback',
            'cbngo.social.facebook' => null,
        ]);

        $response = $this->get(route('contato'));

        $response->assertOk();
        $response->assertSee('href="mailto:fallback@cbngo.org.br"', false);
        $response->assertSee('href="tel:+556230001111"', false);
        $response->assertSee('Rua do Fallback');
        $response->assertSee('aria-label="Instagram da CBN Goiás"', false);
        $response->assertDontSee('aria-label="Facebook da CBN Goiás"', false);
    }

    /**
     * Com a tabela indisponível, o site continua no ar com o fallback.
     */
    public function test_survives_unavailable_table_with_config_fallback(): void
    {
        Schema::drop('institutional_settings');

        $this->get(route('home'))->assertOk()->assertSee('mailto:'.config('cbngo.contact.email'), false);
        $this->get(route('contato'))->assertOk()->assertSee('Rua 04, 515');
    }

    /**
     * Só aparecem as redes preenchidas no painel, cada uma com ícone e aria-label.
     */
    public function test_only_filled_social_networks_are_shown(): void
    {
        InstitutionalSetting::factory()->create([
            'facebook_url' => null,
            'tiktok_url' => 'https://www.tiktok.com/@cbngo',
            'linkedin_url' => null,
        ]);

        $home = $this->get(route('home'));
        $html = (string) $home->getContent();

        $home->assertDontSee('aria-label="Facebook da CBN Goiás"', false);
        $home->assertDontSee('LinkedIn da CBN Goiás', false);
        $this->assertSame(2, substr_count($html, 'aria-label="TikTok da CBN Goiás"'));
        $this->assertSame(2, substr_count($html, 'href="https://www.tiktok.com/@cbngo"'));

        $this->get(route('contato'))
            ->assertSee('href="https://www.tiktok.com/@cbngo"', false)
            ->assertDontSee('facebook.com', false);
    }

    /**
     * O JSON-LD da organização traz o endereço completo e sameAs só com as redes preenchidas.
     */
    public function test_organization_json_ld_has_full_postal_address(): void
    {
        $setting = InstitutionalSetting::factory()->create(['facebook_url' => null]);

        preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', (string) $this->get(route('home'))->getContent(), $matches);
        $organization = collect($matches[1])
            ->map(fn (string $json) => json_decode($json, true, 512, JSON_THROW_ON_ERROR))
            ->first(fn (array $item) => ($item['@type'] ?? null) === ['Organization', 'ReligiousOrganization']);

        $this->assertSame([
            '@type' => 'PostalAddress',
            'streetAddress' => 'Rua 04, 515, 12º andar, sala 1.207, Edifício Parthenon Center - Setor Central',
            'addressLocality' => 'Goiânia',
            'addressRegion' => 'GO',
            'postalCode' => '74020-045',
            'addressCountry' => 'BR',
        ], $organization['address']);
        $this->assertSame($setting->phone, $organization['telephone']);
        $this->assertSame([$setting->instagram_url, $setting->youtube_url], $organization['sameAs']);
    }

    /**
     * Uma edição no painel (updated_at) invalida o cache e o site mostra o dado novo.
     */
    public function test_editing_record_refreshes_cache(): void
    {
        $setting = InstitutionalSetting::factory()->create(['email' => 'antigo@cbngo.org.br']);

        $this->get(route('home'))->assertSee('antigo@cbngo.org.br');

        $this->travel(1)->seconds();
        $setting->update(['email' => 'novo@cbngo.org.br']);

        $this->get(route('home'))
            ->assertSee('novo@cbngo.org.br')
            ->assertDontSee('antigo@cbngo.org.br');
    }
}
