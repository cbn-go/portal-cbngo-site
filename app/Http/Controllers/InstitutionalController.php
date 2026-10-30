<?php

namespace App\Http\Controllers;

use App\Models\BoardMember;
use App\Models\Document;
use App\Models\Statute;
use App\Support\ContactLinks;
use Illuminate\View\View;

class InstitutionalController extends Controller
{
    /**
     * Exibe a página Quem Somos com história, declaração doutrinária, pacto e documentos oficiais.
     */
    public function about(): View
    {
        $documents = Document::active()
            ->orderBy('position')
            ->orderBy('title')
            ->get();

        return view('institucional.quem-somos', [
            'documents' => $documents,
            // Com estatuto publicado em Akoma Ntoso, o card do estatuto leva ao texto integral no site.
            'statuteUrl' => Statute::published()->exists() ? route('estatuto.show') : null,
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
    public function contact(): View
    {
        /** @var array{address: string, phone: string, whatsapp: string, email: string, office_hours: string} $contact */
        $contact = config('cbngo.contact');

        return view('institucional.contato', [
            'contact' => $contact,
            'social' => (array) config('cbngo.social'),
            'phoneHref' => ContactLinks::tel($contact['phone']),
            'whatsappUrl' => ContactLinks::whatsapp($contact['whatsapp']),
            'mapsUrl' => ContactLinks::maps($contact['address']),
        ]);
    }
}
