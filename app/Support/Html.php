<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class Html
{
    /**
     * Elementos de texto do RichEditor do painel liberados no conteúdo de notícias e artigos.
     *
     * @var array<string, array<int, string>>
     */
    private const ALLOWED_ELEMENTS = [
        'p' => [], 'br' => [], 'hr' => [],
        'h2' => [], 'h3' => [], 'h4' => [],
        'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'u' => [], 's' => [], 'del' => [], 'sub' => [], 'sup' => [], 'mark' => [],
        'ul' => [], 'ol' => [], 'li' => [],
        'blockquote' => [], 'pre' => [], 'code' => [],
        'table' => [], 'thead' => [], 'tbody' => [], 'tr' => [], 'th' => ['colspan', 'rowspan'], 'td' => ['colspan', 'rowspan'],
        'figure' => [], 'figcaption' => [],
        'a' => ['href', 'title'],
        'img' => ['src', 'alt', 'title', 'width', 'height'],
    ];

    private static ?HtmlSanitizer $sanitizer = null;

    /**
     * Sanitiza o HTML do RichEditor (defesa em profundidade: o painel também sanitiza ao salvar).
     */
    public static function sanitize(?string $html): string
    {
        $normalized = self::rewriteRelativeImageSources((string) $html);

        return self::sanitizer()->sanitize($normalized);
    }

    /**
     * Normaliza atributos src relativos em tags <img> para apontarem para MEDIA_URL.
     */
    private static function rewriteRelativeImageSources(string $html): string
    {
        return (string) preg_replace_callback(
            '/<img\b[^>]*>/i',
            static fn (array $imgMatch): string => (string) preg_replace_callback(
                '/\bsrc\s*=\s*(["\'])(.*?)\1/i',
                static function (array $srcMatch): string {
                    $resolved = Media::url(html_entity_decode($srcMatch[2], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                    if ($resolved === null) {
                        return $srcMatch[0];
                    }

                    return 'src="'.htmlspecialchars($resolved, ENT_QUOTES, 'UTF-8').'"';
                },
                $imgMatch[0],
            ),
            $html,
        );
    }

    private static function sanitizer(): HtmlSanitizer
    {
        if (self::$sanitizer instanceof HtmlSanitizer) {
            return self::$sanitizer;
        }

        $config = (new HtmlSanitizerConfig)
            ->allowLinkSchemes(['http', 'https', 'mailto'])
            ->allowMediaSchemes(['http', 'https'])
            ->forceAttribute('a', 'rel', 'noopener noreferrer');

        foreach (self::ALLOWED_ELEMENTS as $element => $attributes) {
            $config = $config->allowElement($element, $attributes);
        }

        return self::$sanitizer = new HtmlSanitizer($config);
    }
}
