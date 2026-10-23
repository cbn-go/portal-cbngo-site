<?php

namespace Tests\Feature;

use App\Models\BoardMember;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstitutionalPagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Valida que /quem-somos exibe as seções de História, Declaração Doutrinária e Pacto de Fé (AC 7.1).
     */
    public function test_about_page_renders_history_doctrine_and_covenant_sections(): void
    {
        $this->withoutVite();

        $response = $this->get(route('quem-somos'));

        $response->assertStatus(200);
        $response->assertSee('id="historia"', false);
        $response->assertSee('id="declaracao-doutrinaria"', false);
        $response->assertSee('id="pacto"', false);
        $response->assertSee('id="documentos"', false);
        $response->assertSee('Declaração Doutrinária');
        $response->assertSee('1967');
        $response->assertSee('href="'.route('diretoria').'"', false);
    }

    /**
     * Valida que os documentos oficiais ativos são listados com link de consulta/download e inativos são omitidos (AC 7.1).
     */
    public function test_about_page_lists_active_documents_with_download_links(): void
    {
        $this->withoutVite();

        Document::create([
            'title' => 'Regimento Interno da CBN',
            'slug' => 'regimento-interno',
            'category' => 'regimento',
            'file_url' => 'https://cbngo.com.br/dados-abertos/regimento-interno.pdf',
            'position' => 2,
            'is_active' => true,
        ]);

        Document::create([
            'title' => 'Estatuto da CBN',
            'slug' => 'estatuto',
            'description' => 'Estatuto social vigente da convenção.',
            'category' => 'estatuto',
            'file_url' => 'https://cbngo.com.br/dados-abertos/estatuto.pdf',
            'position' => 1,
            'is_active' => true,
        ]);

        Document::create([
            'title' => 'Estatuto Revogado 2010',
            'slug' => 'estatuto-revogado-2010',
            'category' => 'estatuto',
            'file_url' => 'https://cbngo.com.br/dados-abertos/estatuto-2010.pdf',
            'is_active' => false,
        ]);

        $response = $this->get(route('quem-somos'));

        $response->assertStatus(200);
        $response->assertSeeInOrder(['Estatuto da CBN', 'Regimento Interno da CBN']);
        $response->assertSee('Estatuto social vigente da convenção.');
        $response->assertSee('href="https://cbngo.com.br/dados-abertos/estatuto.pdf"', false);
        $response->assertSee('href="https://cbngo.com.br/dados-abertos/regimento-interno.pdf"', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertDontSee('Estatuto Revogado 2010');
        $response->assertDontSee('estatuto-2010.pdf', false);
    }

    /**
     * Valida o empty state quando não há documentos oficiais publicados.
     */
    public function test_about_page_shows_empty_state_without_documents(): void
    {
        $this->withoutVite();

        $response = $this->get(route('quem-somos'));

        $response->assertStatus(200);
        $response->assertSee('data-testid="documents-empty"', false);
    }

    /**
     * Valida que URLs de documento com esquemas inseguros não são renderizadas como links (proteção XSS).
     */
    public function test_about_page_does_not_link_unsafe_document_urls(): void
    {
        $this->withoutVite();

        Document::create([
            'title' => 'Documento Malicioso',
            'slug' => 'documento-malicioso',
            'file_url' => 'javascript:alert(1)',
            'is_active' => true,
        ]);

        Document::create([
            'title' => 'Documento Protocolo Relativo',
            'slug' => 'documento-protocolo-relativo',
            'file_url' => '//evil.example.com/arquivo.pdf',
            'is_active' => true,
        ]);

        Document::create([
            'title' => 'Documento Local',
            'slug' => 'documento-local',
            'file_url' => '/docs/manual.pdf',
            'is_active' => true,
        ]);

        $response = $this->get(route('quem-somos'));

        $response->assertStatus(200);
        $response->assertSee('Documento Malicioso');
        $response->assertDontSee('javascript:alert(1)', false);
        $response->assertDontSee('evil.example.com', false);
        $response->assertSee('href="/docs/manual.pdf"', false);
        $response->assertSee('Arquivo indisponível');
    }

    /**
     * Valida que a diretoria é agrupada na ordem institucional dos órgãos e por posição dentro do grupo (AC 7.2).
     */
    public function test_board_page_groups_members_in_institutional_order(): void
    {
        $this->withoutVite();

        BoardMember::create(['name' => 'Coordenador Norte', 'role' => 'Coordenador Regional', 'group' => 'coordenacao_regional', 'region' => 'Região Norte']);
        BoardMember::create(['name' => 'Relator Fiscal', 'role' => 'Relator', 'group' => 'conselho_fiscal']);
        BoardMember::create(['name' => 'Tesoureiro Geral', 'role' => '1º Tesoureiro', 'group' => 'tesouraria']);
        BoardMember::create(['name' => 'Segundo Secretário', 'role' => '2º Secretário', 'group' => 'secretaria', 'position' => 2]);
        BoardMember::create(['name' => 'Primeiro Secretário', 'role' => '1º Secretário', 'group' => 'secretaria', 'position' => 1]);
        BoardMember::create(['name' => 'Vice Presidente Um', 'role' => '1º Vice-presidente', 'group' => 'vice_presidencia']);
        BoardMember::create(['name' => 'Presidente Atual', 'role' => 'Presidente', 'group' => 'presidencia']);

        $response = $this->get(route('diretoria'));

        $response->assertStatus(200);
        $response->assertSeeInOrder([
            'data-board-group="presidencia"',
            'data-board-group="vice_presidencia"',
            'data-board-group="secretaria"',
            'data-board-group="tesouraria"',
            'data-board-group="conselho_fiscal"',
            'data-board-group="coordenacao_regional"',
        ], false);
        $response->assertSeeInOrder(['Presidência', 'Vice-presidências', 'Secretaria', 'Tesouraria', 'Conselho Fiscal', 'Coordenadores de Regiões']);
        $response->assertSeeInOrder(['Primeiro Secretário', 'Segundo Secretário']);
        $response->assertSee('Presidente Atual');
    }

    /**
     * Valida que membros inativos ou de órgãos desconhecidos não aparecem, e grupos vazios não são renderizados.
     */
    public function test_board_page_hides_inactive_members_and_unknown_groups(): void
    {
        $this->withoutVite();

        BoardMember::create(['name' => 'Presidente Atual', 'role' => 'Presidente', 'group' => 'presidencia']);
        BoardMember::create(['name' => 'Ex Presidente', 'role' => 'Presidente', 'group' => 'presidencia', 'is_active' => false]);
        BoardMember::create(['name' => 'Membro Avulso', 'role' => 'Assessor', 'group' => 'grupo_inexistente']);

        $response = $this->get(route('diretoria'));

        $response->assertStatus(200);
        $response->assertSee('Presidente Atual');
        $response->assertDontSee('Ex Presidente');
        $response->assertDontSee('Membro Avulso');
        $response->assertDontSee('data-board-group="tesouraria"', false);
    }

    /**
     * Valida o card de membro: foto segura, fallback de iniciais e região do coordenador (AC 7.2).
     */
    public function test_board_member_card_renders_initials_fallback_and_region(): void
    {
        $this->withoutVite();

        BoardMember::create([
            'name' => 'Pr. Roberto Silveira',
            'role' => 'Presidente',
            'group' => 'presidencia',
            'photo_url' => 'https://images.example.com/roberto.jpg',
            'term_label' => 'Gestão 2025–2027',
        ]);

        BoardMember::create([
            'name' => 'Pra. Helena de Albuquerque',
            'role' => 'Coordenadora Regional',
            'group' => 'coordenacao_regional',
            'region' => 'Região Sudoeste',
            'photo_url' => 'javascript:alert(1)',
        ]);

        $response = $this->get(route('diretoria'));

        $response->assertStatus(200);
        $response->assertSee('src="https://images.example.com/roberto.jpg"', false);
        $response->assertSee('Gestão 2025–2027');
        $response->assertSee('data-testid="board-member-initials"', false);
        $response->assertSee('>HA<', false);
        $response->assertSee('Região Sudoeste');
        $response->assertDontSee('javascript:alert(1)', false);

        $this->assertSame('RS', (new BoardMember(['name' => 'Pr. Roberto Silveira']))->getInitials());
        $this->assertSame('HA', (new BoardMember(['name' => 'Pra. Helena de Albuquerque']))->getInitials());
        $this->assertSame('M', (new BoardMember(['name' => 'Marta']))->getInitials());
    }

    /**
     * Valida o empty state da diretoria quando não há membros ativos.
     */
    public function test_board_page_shows_empty_state_without_members(): void
    {
        $this->withoutVite();

        $response = $this->get(route('diretoria'));

        $response->assertStatus(200);
        $response->assertSee('data-testid="board-empty"', false);
    }

    /**
     * Valida que /contato exibe os dados oficiais da sede a partir de config('cbngo.contact') e não possui formulário (AC 7.3).
     */
    public function test_contact_page_renders_official_contact_data_from_config(): void
    {
        $this->withoutVite();

        config([
            'cbngo.contact.address' => 'Rua Teste, 100 - Setor Central, Goiânia - GO',
            'cbngo.contact.phone' => '(62) 3200-1234',
            'cbngo.contact.whatsapp' => '(62) 98487-6863',
            'cbngo.contact.email' => 'contato@cbngo.org.br',
            'cbngo.contact.office_hours' => 'Segunda a sexta, das 9h às 18h30',
        ]);

        $response = $this->get(route('contato'));

        $response->assertStatus(200);
        $response->assertSee('Rua Teste, 100 - Setor Central, Goiânia - GO');
        $response->assertSee('href="tel:+556232001234"', false);
        $response->assertSee('href="https://wa.me/5562984876863"', false);
        $response->assertSee('href="mailto:contato@cbngo.org.br"', false);
        $response->assertSee('Segunda a sexta, das 9h às 18h30');
        $response->assertSee('https://www.google.com/maps/search/?api=1&amp;query='.rawurlencode('Rua Teste, 100 - Setor Central, Goiânia - GO'), false);
        $response->assertDontSee('<form', false);
    }

    /**
     * Valida que o navbar destaca 'Institucional' com aria-current="page" em /quem-somos e /diretoria.
     */
    public function test_navbar_highlights_institutional_on_about_and_board_pages(): void
    {
        $this->withoutVite();

        $pattern = '/href="'.preg_quote(route('quem-somos'), '/').'"[^>]*aria-current="page"/';

        foreach (['quem-somos', 'diretoria'] as $routeName) {
            $response = $this->get(route($routeName));

            $response->assertStatus(200);
            $this->assertMatchesRegularExpression($pattern, (string) $response->getContent());
        }
    }

    /**
     * Valida que o navbar destaca 'Contato' com aria-current="page" em /contato.
     */
    public function test_navbar_highlights_contact_on_contact_page(): void
    {
        $this->withoutVite();

        $response = $this->get(route('contato'));

        $response->assertStatus(200);
        $this->assertMatchesRegularExpression(
            '/href="'.preg_quote(route('contato'), '/').'"[^>]*aria-current="page"/',
            (string) $response->getContent()
        );
    }

    /**
     * Valida que barra superior e rodapé usam o e-mail oficial único do config e que o rodapé não colide com a âncora #contato.
     */
    public function test_layout_uses_official_contact_email_everywhere(): void
    {
        $this->withoutVite();

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('mailto:'.config('cbngo.contact.email'), false);
        $response->assertDontSee('secretaria@cbngo.org.br', false);
        $response->assertDontSee('contato@cbngo.com.br', false);
        $response->assertDontSee('id="contato"', false);
        $this->assertSame('contato@cbngo.org.br', config('cbngo.contact.email'));
    }

    /**
     * Valida que o DatabaseSeeder popula a diretoria em todos os órgãos e os documentos oficiais.
     */
    public function test_database_seeder_populates_board_members_and_documents(): void
    {
        $this->withoutVite();

        $this->seed();

        foreach (array_keys(BoardMember::GROUPS) as $group) {
            $this->assertTrue(BoardMember::active()->where('group', $group)->exists(), "Grupo {$group} sem membros no seed.");
        }

        $this->assertTrue(Document::where('category', 'estatuto')->exists());
        $this->assertTrue(Document::where('category', 'regimento')->exists());
        $this->assertTrue(Document::where('category', 'pacto')->exists());

        $this->get(route('diretoria'))->assertStatus(200)->assertSee('Presidência');
        $this->get(route('quem-somos'))->assertStatus(200)->assertSee('Estatuto');
    }
}
