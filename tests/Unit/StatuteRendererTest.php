<?php

namespace Tests\Unit;

use App\Support\Akn\StatuteRenderer;
use InvalidArgumentException;
use Tests\TestCase;

class StatuteRendererTest extends TestCase
{
    private function fixture(): string
    {
        return (string) file_get_contents(base_path('tests/fixtures/estatuto-2025-10-18.akn.xml'));
    }

    /**
     * Valida contagens, sumário, fecho e URIs FRBR do estatuto vigente.
     */
    public function test_renders_structure_toc_and_frbr_metadata(): void
    {
        $document = (new StatuteRenderer)->render($this->fixture());

        $this->assertSame(21, $document->articleCount);
        $this->assertCount(6, $document->toc);
        $this->assertSame(5, array_sum(array_map(fn (array $chapter) => count($chapter['sections']), $document->toc)));
        $this->assertSame('chp_3', $document->toc[2]['eId']);
        $this->assertSame('chp_3__sec_2', $document->toc[2]['sections'][1]['eId']);
        $this->assertSame('Da Diretoria', $document->toc[2]['sections'][1]['heading']);
        $this->assertSame('Anápolis, 18 de outubro de 2025.', $document->conclusions);
        $this->assertSame('/akn/br/act/estatuto/cbngo/2025-10-18', $document->workUri);
        $this->assertSame('/akn/br/act/estatuto/cbngo/2025-10-18/por@2025-10-18', $document->expressionUri);
    }

    /**
     * Valida que os ids são os eIds do AKN, únicos, e que incisos e alíneas viram listas aninhadas.
     */
    public function test_ids_are_unique_akn_eids_and_points_are_nested_lists(): void
    {
        $html = (new StatuteRenderer)->render($this->fixture())->html;

        preg_match_all('# id="([^"]+)"#', $html, $ids);
        $this->assertSame(count($ids[1]), count(array_unique($ids[1])));
        $this->assertMatchesRegularExpression(
            '#<li id="art_8__point_I" class="akn-point">.*?<ol class="akn-points"><li id="art_8__point_I__point_a" class="akn-point">#s',
            $html
        );
        $this->assertStringContainsString('<div id="art_5__para_uniq" class="akn-paragraph"><span class="akn-num">Parágrafo Único.</span>', $html);
    }

    /**
     * Valida que XML inválido ou que não é um ato Akoma Ntoso é rejeitado.
     */
    public function test_rejects_invalid_or_non_akn_xml(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new StatuteRenderer)->render('<html><body>não é AKN</body></html>');
    }

    /**
     * Valida que entidades externas não são carregadas (XXE).
     */
    public function test_does_not_expand_external_entities(): void
    {
        $xml = str_replace(
            '<akomaNtoso',
            '<!DOCTYPE akomaNtoso [<!ENTITY xxe SYSTEM "file:///etc/hosts">]><akomaNtoso',
            $this->fixture()
        );
        $xml = str_replace('<p>A CBN-GO é representada', '<p>&xxe; A CBN-GO é representada', $xml);

        $html = (new StatuteRenderer)->render($xml)->html;

        $this->assertStringNotContainsString('localhost', $html);
        $this->assertStringContainsString('A CBN-GO é representada', $html);
    }
}
