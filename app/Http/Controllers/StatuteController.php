<?php

namespace App\Http\Controllers;

use App\Models\Statute;
use App\Support\Akn\RenderedStatute;
use App\Support\Akn\StatuteRenderer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StatuteController extends Controller
{
    public function __construct(private readonly StatuteRenderer $renderer) {}

    /**
     * Exibe o estatuto vigente renderizado a partir do Akoma Ntoso 3.0 publicado no painel.
     */
    public function show(): View
    {
        $statute = Statute::published()->firstOrFail();

        return $this->page($statute);
    }

    /**
     * Entrega o XML Akoma Ntoso 3.0 da versão vigente, exatamente como está no banco.
     */
    public function xml(): Response
    {
        $statute = Statute::published()->firstOrFail();

        return response($statute->akn_xml, 200, [
            'Content-Type' => 'application/akn+xml; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="estatuto-cbngo-'.$statute->versionKey().'.akn.xml"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Lista a versão vigente e as versões anteriores do estatuto.
     */
    public function versions(): View
    {
        $versions = Statute::history()
            ->get(['id', 'title', 'version_label', 'approved_at', 'approved_place', 'approval_body', 'effective_at', 'status']);

        abort_if($versions->isEmpty(), 404);

        return view('estatuto.versoes', ['versions' => $versions]);
    }

    /**
     * Exibe uma versão pelo dia de aprovação (Y-m-d), com aviso quando não é a vigente.
     */
    public function version(string $date): View
    {
        abort_unless(preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1, 404);

        $statute = Statute::history()->whereDate('approved_at', $date)->firstOrFail();

        return $this->page($statute);
    }

    private function page(Statute $statute): View
    {
        return view('estatuto.show', [
            'statute' => $statute,
            'document' => $this->rendered($statute),
            'isCurrent' => $statute->isCurrent(),
            'hasHistory' => Statute::history()->count() > 1,
        ]);
    }

    /**
     * HTML renderizado em cache, versionado pelo updated_at: publicar ou editar no painel gera nova chave.
     */
    private function rendered(Statute $statute): RenderedStatute
    {
        $key = 'estatuto.'.$statute->id.'.'.md5((string) $statute->updated_at?->toIso8601String());

        try {
            return Cache::remember($key, (int) config('cbngo.statute.cache_ttl'), fn () => $this->renderer->render($statute->akn_xml));
        } catch (InvalidArgumentException $exception) {
            Log::error('Estatuto com XML Akoma Ntoso inválido.', ['statute_id' => $statute->id, 'error' => $exception->getMessage()]);

            throw new NotFoundHttpException;
        }
    }
}
