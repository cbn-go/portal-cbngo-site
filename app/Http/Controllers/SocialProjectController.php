<?php

namespace App\Http\Controllers;

use App\Enums\ProjectCategory;
use App\Models\Church;
use App\Models\SocialProject;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SocialProjectController extends Controller
{
    /**
     * Exibe os projetos sociais publicados das igrejas, com filtros compartilháveis na query string.
     */
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $categoria = ProjectCategory::fromSlug(trim((string) $request->query('categoria', '')));
        $cidade = trim((string) $request->query('cidade', ''));
        $igreja = trim((string) $request->query('igreja', ''));

        $query = SocialProject::published()->with('church');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('summary', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($categoria !== null) {
            $query->where('category', $categoria->value);
        }

        if ($cidade !== '') {
            $query->where('city', $cidade);
        }

        if ($igreja !== '') {
            $query->whereHas('church', fn ($church) => $church->where('slug', $igreja));
        }

        $projects = $query->ordered()->paginate(9)->withQueryString();

        $availableCities = SocialProject::published()
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        /** @var Collection<string, string> $availableChurches */
        $availableChurches = Church::active()
            ->whereIn('id', SocialProject::published()->select('church_id'))
            ->orderBy('name')
            ->pluck('name', 'slug');

        return view('projetos-sociais.index', [
            'projects' => $projects,
            'categories' => ProjectCategory::cases(),
            'availableCities' => $availableCities,
            'availableChurches' => $availableChurches,
            'q' => $q,
            'categoria' => $categoria,
            'cidade' => $cidade,
            'igreja' => $igreja,
        ]);
    }

    /**
     * Exibe o projeto social com ficha, galeria, contato para ajudar e outros projetos da mesma categoria.
     */
    public function show(string $slug): View
    {
        $project = SocialProject::published()->with('church')->where('slug', $slug)->firstOrFail();

        $relatedProjects = SocialProject::published()
            ->with('church')
            ->where('category', $project->category->value)
            ->where('id', '!=', $project->id)
            ->ordered()
            ->take(3)
            ->get();

        return view('projetos-sociais.show', [
            'project' => $project,
            'relatedProjects' => $relatedProjects,
        ]);
    }
}
