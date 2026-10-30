<?php

namespace App\Http\Controllers;

use App\Models\BoardMember;
use App\Models\Document;
use App\Models\Statute;
use App\Support\InstitutionalInfo;
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
     * Exibe a galeria da diretoria atual agrupada pelos órgãos da convenção.
     */
    public function board(): View
    {
        $members = BoardMember::active()
            ->whereIn('group', array_keys(BoardMember::GROUPS))
            ->orderBy('position')
            ->orderBy('name')
            ->get()
            ->groupBy('group');

        $groups = [];
        foreach (BoardMember::GROUPS as $key => $label) {
            if ($members->has($key)) {
                $groups[$key] = [
                    'label' => $label,
                    'members' => $members->get($key),
                ];
            }
        }

        return view('institucional.diretoria', [
            'groups' => $groups,
        ]);
    }

    /**
     * Exibe os dados oficiais de contato da sede estadual.
     */
    public function contact(InstitutionalInfo $info): View
    {
        return view('institucional.contato', ['info' => $info]);
    }
}
