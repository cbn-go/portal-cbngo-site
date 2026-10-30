<?php

namespace Tests\Feature;

use App\Models\BoardMember;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
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
            'external_url' => 'https://cbngo.com.br/dados-abertos/regimento-interno.pdf',
            'file_format' => 'Página',
            'position' => 2,
            'is_active' => true,
        ]);

        Document::create([
            'title' => 'Ata da Assembleia Geral 2025',
            'slug' => 'ata-assembleia-2025',
            'description' => 'Ata da assembleia geral ordinária.',
            'category' => 'ata',
            'file' => 'documents/files/ata-2025.pdf',
            'position' => 1,
            'is_active' => true,
        ]);

        Document::create([
            'title' => 'Ata Revogada 2010',
            'slug' => 'ata-revogada-2010',
            'category' => 'ata',
            'file' => 'documents/files/ata-2010.pdf',
            'is_active' => false,
        ]);

        $response = $this->get(route('quem-somos'));

        $response->assertStatus(200);
        $response->assertSeeInOrder(['Ata da Assembleia Geral 2025', 'Regimento Interno da CBN']);
        $response->assertSee('Ata da assembleia geral ordinária.');
        $response->assertSee('href="https://midia.test/documents/files/ata-2025.pdf"', false);
        $response->assertSee('href="https://cbngo.com.br/dados-abertos/regimento-interno.pdf"', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertDontSee('Ata Revogada 2010');
        $response->assertDontSee('ata-2010.pdf', false);
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
            'external_url' => 'javascript:alert(1)',
            'is_active' => true,
        ]);

        Document::create([
            'title' => 'Documento Protocolo Relativo',
            'slug' => 'documento-protocolo-relativo',
            'external_url' => '//evil.example.com/arquivo.pdf',
            'is_active' => true,
        ]);

        Document::create([
            'title' => 'Documento Local',
            'slug' => 'documento-local',
            'external_url' => '/docs/manual.pdf',
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
     * Valida que a diretoria é agrupada na ordem institucional dos órgãos, com o CONPLEX, e por posição dentro do grupo (#45).
     */
    public function test_board_page_groups_members_in_institutional_order(): void
    {
        $this->withoutVite();

        BoardMember::factory()->create(['name' => 'Coordenador Leste', 'role' => 'Coordenador', 'group' => 'coordenacao_regional', 'region' => 'leste']);
        BoardMember::factory()->create(['name' => 'Relator Fiscal', 'role' => 'Relator', 'group' => 'conselho_fiscal']);
        BoardMember::factory()->create(['name' => 'Vogal Conplex', 'role' => 'Vogal', 'group' => 'conplex']);
        BoardMember::factory()->create(['name' => 'Tesoureiro Geral', 'role' => '1º Tesoureiro', 'group' => 'tesouraria']);
        BoardMember::factory()->create(['name' => 'Segundo Secretário', 'role' => '2º Secretário de Atas', 'group' => 'secretaria', 'position' => 2]);
        BoardMember::factory()->create(['name' => 'Primeiro Secretário', 'role' => '1º Secretário de Atas', 'group' => 'secretaria', 'position' => 1]);
        BoardMember::factory()->create(['name' => 'Vice Presidente', 'role' => 'Vice-Presidente', 'group' => 'vice_presidencia']);
        BoardMember::factory()->create(['name' => 'Presidente Atual', 'role' => 'Presidente', 'group' => 'presidencia']);

        $response = $this->get(route('diretoria'));

        $response->assertStatus(200);
        $response->assertSeeInOrder([
            'data-board-group="presidencia"',
            'data-board-group="vice_presidencia"',
            'data-board-group="secretaria"',
            'data-board-group="tesouraria"',
            'data-board-group="conplex"',
            'data-board-group="conselho_fiscal"',
            'data-board-group="coordenacao_regional"',
        ], false);
        $response->assertSeeInOrder([
            'Presidência',
            'Vice-presidência',
            'Secretaria',
            'Tesouraria',
            'Conselho de Planejamento e Execução (CONPLEX)',
            'Conselho Fiscal',
            'Diretorias Regionais (AMIGO)',
        ]);
        $response->assertSeeInOrder(['Primeiro Secretário', 'Segundo Secretário']);
        $response->assertSee('Vogal Conplex');
    }

    /**
     * Valida que o título eclesiástico aparece antes do nome, que o leigo fica sem prefixo e que as iniciais ignoram o título (#45).
     */
    public function test_board_member_card_shows_title_before_name(): void
    {
        $this->withoutVite();

        BoardMember::factory()->create(['title' => 'Pr.', 'name' => 'André Luiz Rodrigues da Silva', 'role' => 'Presidente', 'group' => 'presidencia', 'photo' => 'board/photos/andre.jpg']);
        BoardMember::factory()->create(['title' => null, 'name' => 'Irenice Ramos', 'role' => 'Vogal', 'group' => 'conplex']);

        $response = $this->get(route('diretoria'));

        $response->assertSee('>Pr. André Luiz Rodrigues da Silva<', false);
        $response->assertSee('alt="Foto de Pr. André Luiz Rodrigues da Silva"', false);
        $response->assertSee('>Irenice Ramos<', false);
        $response->assertSee('>IR<', false);

        $this->assertSame('AS', (new BoardMember(['title' => 'Pr.', 'name' => 'André Luiz Rodrigues da Silva']))->getInitials());
        $this->assertSame('Ev. Fábio Oliveira Nascimento', (new BoardMember(['title' => 'Ev.', 'name' => 'Fábio Oliveira Nascimento']))->getDisplayName());
        $this->assertSame('Irenice Ramos', (new BoardMember(['name' => 'Irenice Ramos']))->getDisplayName());
    }

    /**
     * Valida que as regionais são agrupadas por AMIGO na ordem Leste, Oeste, Norte e Sudoeste, e que região antiga não some (#45).
     */
    public function test_board_page_groups_regional_members_by_amigo_region(): void
    {
        $this->withoutVite();
        $log = Log::spy();

        BoardMember::factory()->create(['name' => 'Coordenador Sudoeste', 'role' => 'Coordenador', 'group' => 'coordenacao_regional', 'region' => 'sudoeste', 'position' => 1]);
        BoardMember::factory()->create(['name' => 'Secretário Norte', 'role' => '1º Secretário', 'group' => 'coordenacao_regional', 'region' => 'norte', 'position' => 3]);
        BoardMember::factory()->create(['name' => 'Coordenador Norte', 'role' => 'Coordenador', 'group' => 'coordenacao_regional', 'region' => 'norte', 'position' => 2]);
        BoardMember::factory()->create(['name' => 'Coordenador Oeste', 'role' => 'Coordenador', 'group' => 'coordenacao_regional', 'region' => 'oeste', 'position' => 4]);
        BoardMember::factory()->create(['name' => 'Coordenador Leste', 'role' => 'Coordenador', 'group' => 'coordenacao_regional', 'region' => 'leste', 'position' => 5]);
        BoardMember::factory()->create(['name' => 'Coordenador Antigo', 'role' => 'Coordenador Regional', 'group' => 'coordenacao_regional', 'region' => 'Região Centro (Anápolis)', 'position' => 0]);

        $response = $this->get(route('diretoria'));

        $response->assertSeeInOrder([
            'data-board-region="leste"', 'AMIGO Leste', 'Coordenador Leste',
            'data-board-region="oeste"', 'AMIGO Oeste', 'Coordenador Oeste',
            'data-board-region="norte"', 'AMIGO Norte', 'Coordenador Norte', 'Secretário Norte',
            'data-board-region="sudoeste"', 'AMIGO Sudoeste', 'Coordenador Sudoeste',
            'Região Centro (Anápolis)', 'Coordenador Antigo',
        ], false);
        $response->assertSee('<h4', false);

        $log->shouldHaveReceived('warning')->once();
    }

    /**
     * Valida que membros de órgãos desconhecidos aparecem no fim com rótulo genérico e geram aviso no log (#45).
     */
    public function test_board_page_shows_unknown_groups_with_generic_label_and_logs(): void
    {
        $this->withoutVite();
        $log = Log::spy();

        BoardMember::factory()->create(['name' => 'Membro Avulso', 'role' => 'Assessor', 'group' => 'grupo_inexistente']);
        BoardMember::factory()->create(['name' => 'Presidente Atual', 'role' => 'Presidente', 'group' => 'presidencia']);

        $response = $this->get(route('diretoria'));

        $response->assertSeeInOrder([
            'data-board-group="presidencia"',
            'data-board-group="grupo_inexistente"',
            'Outros membros da diretoria',
            'Membro Avulso',
        ], false);

        $log->shouldHaveReceived('warning')->once()->withArgs(fn (string $message, array $context): bool => $context['group'] === 'grupo_inexistente' && $context['count'] === 1);
    }

    /**
     * Valida que membros inativos não aparecem e grupos vazios não são renderizados.
     */
    public function test_board_page_hides_inactive_members(): void
    {
        $this->withoutVite();

        BoardMember::factory()->create(['name' => 'Presidente Atual', 'role' => 'Presidente', 'group' => 'presidencia']);
        BoardMember::factory()->create(['name' => 'Ex Presidente', 'role' => 'Presidente', 'group' => 'presidencia', 'is_active' => false]);

        $response = $this->get(route('diretoria'));

        $response->assertStatus(200);
        $response->assertSee('Presidente Atual');
        $response->assertDontSee('Ex Presidente');
        $response->assertDontSee('data-board-group="tesouraria"', false);
    }

    /**
     * Valida o card de membro: foto segura, fallback de iniciais e mandato (AC 7.2).
     */
    public function test_board_member_card_renders_photo_and_initials_fallback(): void
    {
        $this->withoutVite();

        BoardMember::factory()->create([
            'title' => 'Pr.',
            'name' => 'Roberto Silveira',
            'role' => 'Presidente',
            'group' => 'presidencia',
            'photo' => 'board/photos/roberto.jpg',
        ]);

        BoardMember::factory()->create([
            'title' => 'Pra.',
            'name' => 'Helena de Albuquerque',
            'role' => '1ª Secretária de Atas',
            'group' => 'secretaria',
            'photo' => 'javascript:alert(1)',
        ]);

        $response = $this->get(route('diretoria'));

        $response->assertStatus(200);
        $response->assertSee('src="https://midia.test/board/photos/roberto.jpg"', false);
        $response->assertSee('Triênio 2026–2029');
        $response->assertSee('data-testid="board-member-initials"', false);
        $response->assertSee('>HA<', false);
        $response->assertDontSee('javascript:alert(1)', false);

        // Nomes antigos que ainda trazem o título no próprio campo.
        $this->assertSame('RS', (new BoardMember(['name' => 'Pr. Roberto Silveira']))->getInitials());
        $this->assertSame('FN', (new BoardMember(['name' => 'Ev. Fábio Nascimento']))->getInitials());
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
     * Valida que /contato exibe os dados oficiais da sede (fallback do config, sem registro do painel) e não possui formulário (AC 7.3).
     */
    public function test_contact_page_renders_official_contact_data_from_config(): void
    {
        $this->withoutVite();

        config([
            'cbngo.contact.street' => 'Rua Teste',
            'cbngo.contact.number' => '100',
            'cbngo.contact.complement' => null,
            'cbngo.contact.phone' => '(62) 3200-1234',
            'cbngo.contact.whatsapp' => '(62) 98487-6863',
            'cbngo.contact.email' => 'contato@cbngo.org.br',
            'cbngo.contact.office_hours' => 'Segunda a sexta, das 9h às 18h30',
        ]);

        $response = $this->get(route('contato'));

        $response->assertStatus(200);
        $response->assertSee('Rua Teste, 100');
        $response->assertSee('Setor Central, Goiânia - GO');
        $response->assertSee('href="tel:+556232001234"', false);
        $response->assertSee('href="https://wa.me/5562984876863"', false);
        $response->assertSee('href="mailto:contato@cbngo.org.br"', false);
        $response->assertSee('Segunda a sexta, das 9h às 18h30');
        $response->assertSee('https://www.google.com/maps/search/?api=1&amp;query='.rawurlencode('Rua Teste, 100 - Setor Central, Goiânia - GO, CEP 74020-045'), false);
        // A página de contato não tem formulário (o menu tem a busca do portal, fora do <main>).
        preg_match('#<main[^>]*>(.*)</main>#s', (string) $response->getContent(), $main);
        $this->assertStringNotContainsString('<form', $main[1]);
    }

    /**
     * Valida que o navbar destaca o grupo 'A Convenção' e o link da página em /quem-somos e /diretoria.
     */
    public function test_navbar_highlights_institutional_on_about_and_board_pages(): void
    {
        $this->withoutVite();

        foreach (['quem-somos', 'diretoria'] as $routeName) {
            $response = $this->get(route($routeName));
            $html = (string) $response->getContent();

            $response->assertStatus(200);
            // O link da própria página fica com aria-current e o grupo "A Convenção" fica destacado.
            $this->assertMatchesRegularExpression('/href="'.preg_quote(route($routeName), '/').'"[^>]*aria-current="page"/', $html);
            $this->assertStringContainsString('<span class="border-b-2 border-cbn-black">A Convenção</span>', $html);
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
     * Valida que o DatabaseSeeder traz a diretoria do triênio 2026–2029 (30 membros) e os documentos oficiais (#45).
     */
    public function test_database_seeder_populates_current_board_and_documents(): void
    {
        $this->withoutVite();

        $this->seed();

        $this->assertSame(30, BoardMember::active()->count());
        $this->assertEquals(
            ['presidencia' => 1, 'vice_presidencia' => 1, 'secretaria' => 2, 'conplex' => 4, 'conselho_fiscal' => 6, 'coordenacao_regional' => 16],
            BoardMember::active()->get()->countBy('group')->all()
        );
        $this->assertEquals(
            ['leste' => 4, 'oeste' => 4, 'norte' => 4, 'sudoeste' => 4],
            BoardMember::active()->where('group', 'coordenacao_regional')->get()->countBy('region')->all()
        );

        $this->assertTrue(Document::where('category', 'estatuto')->exists());
        $this->assertTrue(Document::where('category', 'regimento')->exists());
        $this->assertTrue(Document::where('category', 'pacto')->exists());

        $html = (string) $this->get(route('diretoria'))->assertStatus(200)->getContent();
        $this->assertSame(30, substr_count($html, '<article'));
        $this->assertStringContainsString('Pr. André Luiz Rodrigues da Silva', $html);
        $this->assertStringContainsString('Irenice Ramos', $html);
        $this->assertStringContainsString('Allan Kardec Pereira de Assis', $html);

        $this->get(route('quem-somos'))->assertStatus(200)->assertSee('Estatuto');
    }
}
