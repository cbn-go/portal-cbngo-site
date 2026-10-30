<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Statute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatuteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    /**
     * A página renderiza o estatuto vigente com os 21 artigos do Akoma Ntoso, cabeçalho e sumário.
     */
    public function test_statute_page_renders_published_version_from_akoma_ntoso(): void
    {
        Statute::factory()->create();

        $response = $this->get('/estatuto');
        $html = (string) $response->getContent();

        $response->assertOk();
        $response->assertSee('Estatuto da Convenção Batista Nacional do Estado de Goiás');
        $response->assertSee('Assembleia Geral · Anápolis, 18 de outubro de 2025');
        $response->assertSee('Ata da Assembleia Geral Extraordinária de 18 de outubro de 2025');
        $response->assertSee('registro nº 806');
        $response->assertSee('Anápolis, 18 de outubro de 2025.');
        $response->assertSee('Em vigor');

        preg_match('#<div[^>]*data-testid="statute-body"[^>]*>(.*)#s', $html, $body);
        $this->assertSame(21, preg_match_all('#<article id="art_\d+"#', $body[1]));
        $this->assertSame(1, substr_count($html, '<h1'));
        $this->assertSame(6, preg_match_all('#<section id="chp_\d+" class="akn-chapter"#', $html));
        $this->assertSame(5, preg_match_all('#<section id="chp_\d+__sec_\d+" class="akn-section"#', $html));
    }

    /**
     * Cada elemento recebe o eId do AKN como id, e o sumário aponta para capítulos e seções.
     */
    public function test_statute_page_exposes_akn_eids_as_anchors_and_toc(): void
    {
        Statute::factory()->create();

        $response = $this->get('/estatuto');

        $response->assertSee('id="chp_3__sec_2"', false);
        $response->assertSee('id="art_8__point_I__point_a"', false);
        $response->assertSee('id="art_7__para_1"', false);
        $response->assertSee('href="#art_12"', false);
        $response->assertSee('data-eid="art_12"', false);
        $response->assertSee('Copiar link do Art. 12', false);
        $response->assertSee('data-toc-link="chp_3__sec_2"', false);
        $response->assertSee('Seção II · Da Diretoria');
    }

    /**
     * O XML baixado é idêntico ao do banco, com o tipo de mídia do Akoma Ntoso.
     */
    public function test_xml_download_returns_database_xml_byte_for_byte(): void
    {
        $statute = Statute::factory()->create();

        $response = $this->get('/estatuto.akn.xml');

        $response->assertOk();
        $this->assertSame($statute->akn_xml, $response->getContent());
        $this->assertStringStartsWith('application/akn+xml', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('estatuto-cbngo-2025-10-18.akn.xml', (string) $response->headers->get('Content-Disposition'));
        $this->get('/estatuto')->assertSee(route('estatuto.xml'), false)->assertSee('Baixar XML')->assertDontSee('Akoma Ntoso');
    }

    /**
     * Sem versão publicada (só rascunho), a página e o XML respondem 404.
     */
    public function test_statute_routes_return_404_without_published_version(): void
    {
        $this->get('/estatuto')->assertNotFound();
        $this->get('/estatuto.akn.xml')->assertNotFound();
        $this->get('/estatuto/versoes')->assertNotFound();

        Statute::factory()->draft()->create();

        $this->get('/estatuto')->assertNotFound();
        $this->get('/estatuto.akn.xml')->assertNotFound();
    }

    /**
     * Versões: a lista traz a vigente e as arquivadas; uma versão anterior mostra o aviso de não vigente.
     */
    public function test_versions_list_and_archived_version_warning(): void
    {
        Statute::factory()->create();
        Statute::factory()->archived('2019-03-16')->create(['approved_place' => 'Goiânia']);
        Statute::factory()->draft()->create(['approved_at' => '2026-05-01']);

        $list = $this->get('/estatuto/versoes');
        $list->assertOk();
        $list->assertSeeInOrder(['Em vigor', '18 de outubro de 2025', 'Versão anterior', '16 de março de 2019']);
        $list->assertSee(route('estatuto.version', '2019-03-16'), false);
        $list->assertDontSee('2026-05-01', false);

        $this->get('/estatuto')->assertSee('Versões anteriores');

        $archived = $this->get('/estatuto/versoes/2019-03-16');
        $archived->assertOk();
        $archived->assertSee('Versão não vigente.', false);
        $archived->assertSee('<meta name="robots" content="noindex, follow">', false);
        $archived->assertDontSee('Baixar XML');

        $this->get('/estatuto/versoes/2026-05-01')->assertNotFound();
        $this->get('/estatuto/versoes/abc')->assertNotFound();
    }

    /**
     * Texto do XML é sempre escapado: HTML e scripts dentro do Akoma Ntoso não viram marcação.
     */
    public function test_statute_text_from_xml_is_escaped(): void
    {
        $xml = (string) file_get_contents(base_path('tests/fixtures/estatuto-2025-10-18.akn.xml'));
        $xml = str_replace(
            'A CBN-GO é representada ativa e passivamente',
            'A CBN-GO &lt;script&gt;alert(1)&lt;/script&gt; <i>é</i> representada ativa e passivamente',
            $xml
        );
        Statute::factory()->create(['akn_xml' => $xml]);

        $response = $this->get('/estatuto');

        $response->assertOk();
        $response->assertDontSee('<script>alert(1)</script>', false);
        $response->assertSee('&lt;script&gt;alert(1)&lt;/script&gt; <em>é</em> representada', false);
    }

    /**
     * XML malformado não derruba o site: responde 404.
     */
    public function test_malformed_xml_returns_404(): void
    {
        Statute::factory()->create(['akn_xml' => '<akomaNtoso><act>']);

        $this->get('/estatuto')->assertNotFound();
    }

    /**
     * O HTML fica em cache e é renovado quando a versão é editada no painel (updated_at).
     */
    public function test_rendered_html_cache_is_refreshed_when_statute_is_updated(): void
    {
        $statute = Statute::factory()->create();

        $this->get('/estatuto')->assertSee('genuína renovação espiritual');

        $this->travel(1)->seconds();
        $statute->update(['akn_xml' => str_replace('genuína renovação espiritual', 'autêntica renovação espiritual', $statute->akn_xml)]);

        $this->get('/estatuto')
            ->assertSee('autêntica renovação espiritual')
            ->assertDontSee('genuína renovação espiritual');
    }

    /**
     * Em Quem Somos, o documento "Estatuto" leva ao texto integral quando há versão publicada.
     */
    public function test_about_page_statute_card_links_to_statute_page(): void
    {
        Document::factory()->create([
            'title' => 'Estatuto da CBN-GO',
            'slug' => 'estatuto-da-cbn',
            'category' => 'estatuto',
            'external_url' => 'https://cbngo.com.br/dados-abertos/estatuto.pdf',
            'is_active' => true,
        ]);

        $this->get('/quem-somos')->assertDontSee('Ler o texto integral');

        Statute::factory()->create();

        $response = $this->get('/quem-somos');
        $response->assertSee('Ler o texto integral');
        $this->assertMatchesRegularExpression(
            '#id="documento-estatuto-da-cbn".*?href="'.preg_quote(route('estatuto.show'), '#').'"#s',
            (string) $response->getContent()
        );
    }

    /**
     * Na página do estatuto, o menu destaca o grupo "A Convenção".
     */
    public function test_navbar_highlights_convention_group_on_statute_page(): void
    {
        Statute::factory()->create();

        $this->get('/estatuto')->assertSee('<span class="border-b-2 border-cbn-black">A Convenção</span>', false);
    }
}
