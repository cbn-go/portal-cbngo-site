<?php

namespace App\Support;

use App\Models\Article;
use App\Models\News;
use App\Models\SocialProject;
use App\Models\Statute;
use App\Support\Akn\RenderedStatute;

class StructuredData
{
    /**
     * Organização publicada em todas as páginas como Organization + ReligiousOrganization.
     *
     * @return array<string, mixed>
     */
    public static function organization(): array
    {
        $info = app(InstitutionalInfo::class);

        return [
            '@context' => 'https://schema.org',
            '@type' => ['Organization', 'ReligiousOrganization'],
            '@id' => url('/').'#organizacao',
            'name' => config('cbngo.organization.name'),
            'alternateName' => config('cbngo.organization.short_name'),
            'url' => url('/'),
            'logo' => asset('images/brand/cbn-go.png'),
            'email' => $info->email(),
            'telephone' => $info->phone(),
            'address' => $info->postalAddress(),
            'sameAs' => array_column($info->socialLinks(), 'url'),
        ];
    }

    /**
     * Notícia publicada como NewsArticle.
     *
     * @return array<string, mixed>
     */
    public static function newsArticle(News $news): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $news->title,
            'description' => Seo::description($news->excerpt),
            'image' => [Seo::image($news->featured_image_url)],
            'datePublished' => $news->published_at?->toIso8601String(),
            'dateModified' => ($news->updated_at ?? $news->published_at)?->toIso8601String(),
            'articleSection' => $news->category,
            'mainEntityOfPage' => route('noticias.show', $news->slug),
            'author' => self::publisherReference(),
            'publisher' => self::publisherReference(),
        ];
    }

    /**
     * Artigo pastoral publicado como Article, com o articulista como autor.
     *
     * @return array<string, mixed>
     */
    public static function article(Article $article): array
    {
        $author = $article->author;

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $article->title,
            'description' => Seo::description($article->excerpt),
            'image' => [Seo::image($article->cover_image_url ?? $author?->avatar_url)],
            'datePublished' => $article->published_at?->toIso8601String(),
            'dateModified' => ($article->updated_at ?? $article->published_at)?->toIso8601String(),
            'articleSection' => $article->category,
            'mainEntityOfPage' => route('artigos.show', $article->slug),
            'author' => $author ? [
                '@type' => 'Person',
                'name' => $author->name,
                'url' => route('autores.show', $author->slug),
            ] : self::publisherReference(),
            'publisher' => self::publisherReference(),
        ];
    }

    /**
     * Projeto social publicado como Project (subtipo de Organization), vinculado à igreja responsável.
     *
     * @return array<string, mixed>
     */
    public static function socialProject(SocialProject $project): array
    {
        $church = $project->church;

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Project',
            'name' => $project->title,
            'description' => Seo::description($project->summary),
            'url' => route('projetos-sociais.show', $project->slug),
            'image' => Seo::image($project->cover_image_url),
            'keywords' => $project->category->value,
            'foundingDate' => $project->started_at->toDateString(),
            'dissolutionDate' => $project->ended_at?->toDateString(),
            'areaServed' => [
                '@type' => 'City',
                'name' => $project->city,
            ],
            'parentOrganization' => $church ? array_filter([
                '@type' => ['Organization', 'ReligiousOrganization'],
                'name' => $church->name,
                'url' => $project->church_directory_url,
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $church->getStreetAddress() ?: null,
                    'addressLocality' => $church->city,
                    'addressRegion' => $church->state,
                    'addressCountry' => 'BR',
                ],
                'memberOf' => self::publisherReference(),
            ]) : self::publisherReference(),
        ], fn ($value) => $value !== null);
    }

    /**
     * Estatuto publicado como Legislation, identificado pela URI FRBR Work do Akoma Ntoso.
     *
     * @return array<string, mixed>
     */
    public static function legislation(Statute $statute, RenderedStatute $document, string $url): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Legislation',
            'name' => $statute->title,
            'legislationIdentifier' => $document->workUri,
            'legislationDate' => $statute->approved_at->toDateString(),
            'legislationLegalForce' => $statute->isCurrent() ? 'https://schema.org/InForce' : 'https://schema.org/NotInForce',
            'legislationType' => 'Estatuto',
            'legislationPassedBy' => self::publisherReference(),
            'inLanguage' => 'pt-BR',
            'url' => $url,
            'encoding' => $statute->isCurrent() ? [
                '@type' => 'MediaObject',
                'encodingFormat' => 'application/akn+xml',
                'contentUrl' => route('estatuto.xml'),
            ] : null,
        ], fn ($value) => $value !== null);
    }

    /**
     * Trilha de navegação como BreadcrumbList.
     *
     * @param  array<int, array{name: string, url: string}>  $items
     * @return array<string, mixed>
     */
    public static function breadcrumbs(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_map(fn (array $item, int $index) => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ], $items, array_keys($items)),
        ];
    }

    /**
     * Serializa o JSON-LD escapando <, > e & para que conteúdo do banco não feche a tag <script>.
     *
     * @param  array<string, mixed>  $data
     */
    public static function toJson(array $data): string
    {
        return (string) json_encode(
            $data,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_THROW_ON_ERROR
        );
    }

    /**
     * Referência resumida à organização usada como publisher.
     *
     * @return array<string, mixed>
     */
    private static function publisherReference(): array
    {
        return [
            '@type' => 'Organization',
            '@id' => url('/').'#organizacao',
            'name' => config('cbngo.organization.name'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => asset('images/brand/cbn-go.png'),
            ],
        ];
    }
}
