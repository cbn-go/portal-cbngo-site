<?php

namespace App\Support\Akn;

/**
 * Resultado da renderização de um estatuto em Akoma Ntoso: pronto para a view e para o cache.
 */
final class RenderedStatute
{
    /**
     * @param  array<int, array{eId: string, num: string, heading: string, sections: array<int, array{eId: string, num: string, heading: string}>}>  $toc
     */
    public function __construct(
        public readonly string $title,
        public readonly array $toc,
        public readonly string $html,
        public readonly int $articleCount,
        public readonly string $conclusions,
        public readonly ?string $workUri,
        public readonly ?string $expressionUri,
    ) {}
}
