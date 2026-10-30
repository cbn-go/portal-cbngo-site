<?php

namespace App\Http\Controllers;

use App\Models\BoardMember;
use App\Models\Document;
use App\Models\Statute;
use App\Support\InstitutionalInfo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InstitutionalController extends Controller
{
    /**
     * Exibe a página Quem Somos com história, declaração doutrinária, pacto e documentos oficiais.
     */
    public function about(): View
    {
        $statute = Statute::published()->first(['id', 'title', 'approved_at', 'approved_place', 'approval_body']);

        $documents = Document::active()
            // O estatuto é publicado só como texto no site (/estatuto), nunca como PDF.
            ->where('category', '!=', 'estatuto')
            ->orderBy('position')
            ->orderBy('title')
            ->get();

        return view('institucional.quem-somos', [
            'documents' => $documents,
            'statute' => $statute,
        ]);
    }

    /**
     * Exibe a galeria da diretoria atual agrupada pelos órgãos da convenção e, nas regionais, pelas AMIGOs.
     */
    public function board(): View
    {
        $members = BoardMember::active()
            ->orderBy('position')
            ->orderBy('name')
            ->get()
            // toBase(): o except() da coleção Eloquent compara chaves de model, não as chaves dos grupos.
            ->toBase()
            ->groupBy('group');

        $groups = [];
        foreach (BoardMember::GROUPS as $key => $label) {
            if ($members->has($key)) {
                $groups[$key] = [
                    'label' => $label,
                    'members' => $members->get($key),
                    'regions' => $key === 'coordenacao_regional' ? $this->groupByRegion($members->get($key)) : null,
                ];
            }
        }

        // Órgão que o site ainda não conhece: aparece no fim com rótulo genérico, nunca some em silêncio.
        foreach ($members->except(array_keys(BoardMember::GROUPS)) as $key => $unknown) {
            Log::warning('Diretoria: órgão desconhecido exibido como "Outros membros da diretoria".', [
                'group' => $key,
                'count' => $unknown->count(),
            ]);

            $groups[$key] = [
                'label' => 'Outros membros da diretoria',
                'members' => $unknown,
                'regions' => null,
            ];
        }

        return view('institucional.diretoria', [
            'groups' => $groups,
        ]);
    }

    /**
     * Agrupa as diretorias regionais por AMIGO, na ordem de `BoardMember::REGIONS`.
     *
     * Região vazia ou fora das quatro chaves (dados antigos) vai para o fim, rotulada com o texto original.
     *
     * @param  Collection<int, BoardMember>  $members
     * @return array<string, array{label: string, members: Collection<int, BoardMember>}>
     */
    private function groupByRegion(Collection $members): array
    {
        $byRegion = $members->toBase()->groupBy(fn (BoardMember $member): string => trim((string) $member->region));

        $regions = [];
        foreach (BoardMember::REGIONS as $key => $label) {
            if ($byRegion->has($key)) {
                $regions[$key] = ['label' => $label, 'members' => $byRegion->get($key)];
            }
        }

        foreach ($byRegion->except(array_keys(BoardMember::REGIONS)) as $region => $unknown) {
            Log::warning('Diretoria: região regional fora das AMIGOs exibida no fim.', [
                'region' => $region,
                'count' => $unknown->count(),
            ]);

            $regions[$region === '' ? 'sem-regiao' : Str::slug($region)] = [
                'label' => $region === '' ? 'Sem região definida' : $region,
                'members' => $unknown,
            ];
        }

        return $regions;
    }

    /**
     * Exibe os dados oficiais de contato da sede estadual.
     */
    public function contact(InstitutionalInfo $info): View
    {
        return view('institucional.contato', ['info' => $info]);
    }
}
