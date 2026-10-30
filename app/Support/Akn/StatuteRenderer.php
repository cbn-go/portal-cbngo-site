<?php

namespace App\Support\Akn;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use InvalidArgumentException;

/**
 * Converte o estatuto em Akoma Ntoso 3.0 (fonte da verdade, editado no painel) em HTML para o site.
 *
 * Nada da marcação do XML é copiado: o HTML é montado aqui e todo texto vindo do XML é escapado.
 * Cada elemento estrutural recebe como id o eId do AKN (ex.: art_8__point_I__point_a), usado nas âncoras.
 */
class StatuteRenderer
{
    public const NAMESPACE = 'http://docs.oasis-open.org/legaldocml/ns/akn/3.0';

    /**
     * Elementos inline aceitos dentro de <p>, convertidos para tags HTML seguras.
     *
     * @var array<string, string>
     */
    private const INLINE_TAGS = ['b' => 'strong', 'i' => 'em', 'u' => 'u', 'sup' => 'sup', 'sub' => 'sub'];

    private DOMXPath $xpath;

    /**
     * @throws InvalidArgumentException quando o XML é inválido ou não é um ato Akoma Ntoso
     */
    public function render(string $xml): RenderedStatute
    {
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        // LIBXML_NONET: nunca acessa a rede; sem LIBXML_NOENT, entidades externas não são expandidas.
        $loaded = $document->loadXML($xml, LIBXML_NONET | LIBXML_NOCDATA);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded) {
            throw new InvalidArgumentException('XML do estatuto inválido.');
        }

        $this->xpath = new DOMXPath($document);
        $this->xpath->registerNamespace('akn', self::NAMESPACE);

        $body = $this->first('/akn:akomaNtoso/akn:act/akn:body');

        if (! $body instanceof DOMElement) {
            throw new InvalidArgumentException('O XML não contém um ato Akoma Ntoso 3.0 com <body>.');
        }

        $toc = [];
        $html = '';

        foreach ($this->children($body, 'chapter') as $chapter) {
            $sections = [];
            foreach ($this->children($chapter, 'section') as $section) {
                $sections[] = $this->tocEntry($section);
            }

            $toc[] = $this->tocEntry($chapter) + ['sections' => $sections];
            $html .= $this->chapter($chapter);
        }

        $articleCount = (int) $this->xpath->evaluate('count(//akn:body//akn:article)');

        return new RenderedStatute(
            title: $this->text($this->first('/akn:akomaNtoso/akn:act/akn:preface//akn:docTitle')) ?: 'Estatuto',
            toc: $toc,
            html: $html,
            articleCount: $articleCount,
            conclusions: $this->text($this->first('/akn:akomaNtoso/akn:act/akn:conclusions')),
            workUri: $this->attribute('//akn:identification/akn:FRBRWork/akn:FRBRthis', 'value'),
            expressionUri: $this->attribute('//akn:identification/akn:FRBRExpression/akn:FRBRthis', 'value'),
        );
    }

    private function chapter(DOMElement $chapter): string
    {
        $html = '';
        foreach ($chapter->childNodes as $child) {
            if ($child instanceof DOMElement && $child->localName === 'article') {
                $html .= $this->article($child);
            } elseif ($child instanceof DOMElement && $child->localName === 'section') {
                $html .= $this->section($child);
            }
        }

        return sprintf(
            '<section id="%s" class="akn-chapter" data-toc><h2><span class="akn-label">%s</span>%s</h2>%s</section>',
            $this->eId($chapter),
            e($this->text($this->child($chapter, 'num'))),
            e($this->text($this->child($chapter, 'heading'))),
            $html
        );
    }

    private function section(DOMElement $section): string
    {
        $html = '';
        foreach ($this->children($section, 'article') as $article) {
            $html .= $this->article($article);
        }

        return sprintf(
            '<section id="%s" class="akn-section" data-toc><h3><span class="akn-label">%s</span>%s</h3>%s</section>',
            $this->eId($section),
            e($this->text($this->child($section, 'num'))),
            e($this->text($this->child($section, 'heading'))),
            $html
        );
    }

    private function article(DOMElement $article): string
    {
        $eId = $this->eId($article);
        $num = e($this->text($this->child($article, 'num')));

        return sprintf(
            '<article id="%1$s" class="akn-article">'
            .'<h4 class="akn-article-num"><a href="#%1$s" class="akn-anchor">%2$s</a>'
            .'<button type="button" class="akn-permalink" data-eid="%1$s" aria-label="Copiar link do %2$s" title="Copiar link do %2$s">'
            .'<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.69a4.5 4.5 0 011.24 7.24l-4.5 4.5a4.5 4.5 0 01-6.36-6.36l1.76-1.76m13.06-.37l1.76-1.76a4.5 4.5 0 00-6.36-6.36l-4.5 4.5a4.5 4.5 0 001.24 7.24"/></svg>'
            .'</button></h4><div class="akn-body">%3$s</div></article>',
            $eId,
            $num,
            $this->hierarchy($article)
        );
    }

    /**
     * Conteúdo de article, paragraph e point: intro, content, incisos/alíneas, parágrafos e wrapUp, na ordem do XML.
     */
    private function hierarchy(DOMElement $element): string
    {
        $html = '';
        $points = '';

        foreach ($element->childNodes as $child) {
            if (! $child instanceof DOMElement || $child->namespaceURI !== self::NAMESPACE) {
                continue;
            }

            if ($child->localName !== 'point' && $points !== '') {
                $html .= '<ol class="akn-points">'.$points.'</ol>';
                $points = '';
            }

            $html .= match ($child->localName) {
                'intro', 'content', 'wrapUp' => $this->paragraphs($child),
                'paragraph' => $this->paragraph($child),
                'list' => $this->hierarchy($child),
                default => '',
            };

            if ($child->localName === 'point') {
                $points .= $this->point($child);
            }
        }

        if ($points !== '') {
            $html .= '<ol class="akn-points">'.$points.'</ol>';
        }

        return $html;
    }

    private function paragraph(DOMElement $paragraph): string
    {
        return sprintf(
            '<div id="%s" class="akn-paragraph"><span class="akn-num">%s</span> %s</div>',
            $this->eId($paragraph),
            e($this->text($this->child($paragraph, 'num'))),
            $this->hierarchy($paragraph)
        );
    }

    private function point(DOMElement $point): string
    {
        return sprintf(
            '<li id="%s" class="akn-point"><span class="akn-num">%s</span><div class="akn-text">%s</div></li>',
            $this->eId($point),
            e($this->text($this->child($point, 'num'))),
            $this->hierarchy($point)
        );
    }

    /**
     * Parágrafos de texto de um bloco (intro, content, wrapUp); texto solto vira um único <p>.
     */
    private function paragraphs(DOMElement $block): string
    {
        $html = '';
        foreach ($this->children($block, 'p') as $p) {
            $html .= '<p>'.$this->inline($p).'</p>';
        }

        if ($html === '' && ($text = $this->text($block)) !== '') {
            $html = '<p>'.e($text).'</p>';
        }

        return $html;
    }

    /**
     * Conteúdo inline de um <p>: texto escapado; b/i/u/sup/sub viram tags seguras; o resto vira só o texto.
     */
    private function inline(DOMNode $node): string
    {
        $html = '';
        foreach ($node->childNodes as $child) {
            if ($child instanceof DOMElement) {
                $tag = self::INLINE_TAGS[$child->localName] ?? null;
                $inner = $this->inline($child);
                $html .= $tag ? "<{$tag}>{$inner}</{$tag}>" : $inner;
            } elseif ($child->nodeType === XML_TEXT_NODE || $child->nodeType === XML_CDATA_SECTION_NODE) {
                $html .= e((string) preg_replace('/\s+/u', ' ', (string) $child->nodeValue));
            }
        }

        return trim($html);
    }

    /**
     * @return array{eId: string, num: string, heading: string}
     */
    private function tocEntry(DOMElement $element): array
    {
        return [
            'eId' => $this->eId($element),
            'num' => $this->text($this->child($element, 'num')),
            'heading' => $this->text($this->child($element, 'heading')),
        ];
    }

    /**
     * eId do elemento, limitado a caracteres seguros para id e âncora.
     */
    private function eId(DOMElement $element): string
    {
        return (string) preg_replace('/[^A-Za-z0-9_\-.]/', '', $element->getAttribute('eId'));
    }

    private function text(?DOMNode $node): string
    {
        return $node ? trim((string) preg_replace('/\s+/u', ' ', (string) $node->textContent)) : '';
    }

    private function child(DOMElement $parent, string $name): ?DOMElement
    {
        return $this->children($parent, $name)[0] ?? null;
    }

    /**
     * @return array<int, DOMElement>
     */
    private function children(DOMElement $parent, string $name): array
    {
        $children = [];
        foreach ($parent->childNodes as $child) {
            if ($child instanceof DOMElement && $child->localName === $name && $child->namespaceURI === self::NAMESPACE) {
                $children[] = $child;
            }
        }

        return $children;
    }

    private function first(string $expression): ?DOMNode
    {
        $nodes = $this->xpath->query($expression);

        return $nodes !== false && $nodes->length > 0 ? $nodes->item(0) : null;
    }

    private function attribute(string $expression, string $name): ?string
    {
        $node = $this->first($expression);

        return $node instanceof DOMElement && $node->hasAttribute($name) ? $node->getAttribute($name) : null;
    }
}
