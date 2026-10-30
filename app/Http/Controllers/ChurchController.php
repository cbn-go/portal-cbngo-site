<?php

namespace App\Http\Controllers;

use App\Models\Church;
use App\Models\News;
use App\Models\SocialProject;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChurchController extends Controller
{
    /**
     * Total de municípios do Estado de Goiás segundo a malha municipal do IBGE.
     */
    private const TOTAL_GOIAS_MUNICIPALITIES = 246;

    /**
     * Exibe o guia público e mapa coroplético municipal de congregações batistas nacionais filiadas à CBN Goiás.
     */
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $cidade = trim((string) $request->query('cidade', ''));

        $query = Church::active();

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('neighborhood', 'like', "%{$q}%");
            });
        }

        if ($cidade !== '') {
            $query->where('city', $cidade);
        }

        $orderedQuery = (clone $query)->orderBy('city')->orderBy('name');

        $churches = (clone $orderedQuery)->paginate(9)->withQueryString();

        /** @var array<string, int> $presenceByCity */
        $presenceByCity = (clone $query)
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->get(['city'])
            ->map(fn (Church $church): string => trim((string) $church->city))
            ->filter(fn (string $cityName): bool => $cityName !== '')
            ->countBy()
            ->sortKeys()
            ->map(fn (int $count): int => $count)
            ->all();

        $availableCities = Church::active()
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        return view('igrejas.index', [
            'churches' => $churches,
            'presenceByCity' => $presenceByCity,
            'totalMunicipalities' => self::TOTAL_GOIAS_MUNICIPALITIES,
            'availableCities' => $availableCities,
            'q' => $q,
            'cidade' => $cidade,
        ]);
    }

    /**
     * Exibe a página da igreja ativa, com seus projetos sociais e notícias publicados.
     */
    public function show(string $slug): View
    {
        $church = Church::active()->where('slug', $slug)->firstOrFail();

        return view('igrejas.show', [
            'church' => $church,
            'projects' => SocialProject::published()->where('church_id', $church->id)->ordered()->limit(3)->get(),
            'news' => News::published()->where('church_id', $church->id)->latest('published_at')->limit(3)->get(),
        ]);
    }
}
