<?php

namespace App\Http\Controllers;

use App\Models\Church;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChurchController extends Controller
{
    /**
     * Exibe o guia público e mapa interativo de congregações batistas nacionais filiadas à CBN Goiás.
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

        $orderedQuery = $query->orderBy('city')->orderBy('name');

        $churches = (clone $orderedQuery)->paginate(9)->withQueryString();

        $mapChurches = (clone $orderedQuery)
            ->get()
            ->filter(fn (Church $church): bool => $church->hasValidCoordinates())
            ->values();

        $availableCities = Church::active()
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        return view('igrejas.index', [
            'churches' => $churches,
            'mapChurches' => $mapChurches,
            'availableCities' => $availableCities,
            'q' => $q,
            'cidade' => $cidade,
        ]);
    }
}
