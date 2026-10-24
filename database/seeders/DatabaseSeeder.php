<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Author;
use App\Models\BoardMember;
use App\Models\Church;
use App\Models\Document;
use App\Models\News;
use App\Models\UrgentNotice;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Aviso urgente institucional ativo
        UrgentNotice::create([
            'title' => 'Assembleia Geral Ordinária 2026',
            'message' => 'Inscrições abertas para delegados da 42ª Assembleia Geral Ordinária da CBN Goiás em Anápolis.',
            'link' => '#eventos',
            'link_text' => 'Ver Detalhes do Evento',
            'type' => 'warning',
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(30),
        ]);

        // 2. Articulistas credenciados
        $author1 = Author::create([
            'name' => 'Pr. Roberto Silveira',
            'slug' => 'pr-roberto-silveira',
            'role_title' => 'Pastor Titular & Presidente CBN-GO',
            'bio' => 'Ministro do evangelho, bacharel em Teologia pelo SETEBAN-GO e articulista sobre liderança eclesiástica.',
            'avatar_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=400&q=80',
            'social_links' => [
                'instagram' => 'https://instagram.com/cbngoias',
                'email' => 'presidencia@cbngo.org.br',
            ],
        ]);

        $author2 = Author::create([
            'name' => 'Pr. Carlos Eduardo Mendes',
            'slug' => 'pr-carlos-eduardo-mendes',
            'role_title' => 'Coordenador Teológico',
            'bio' => 'Doutor em Teologia Sistemática e professor de Hermenêutica Bíblica.',
            'avatar_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=400&q=80',
            'social_links' => [
                'email' => 'teologia@cbngo.org.br',
            ],
        ]);

        $author3 = Author::create([
            'name' => 'Pra. Helena Albuquerque',
            'slug' => 'pra-helena-albuquerque',
            'role_title' => 'Líder de Missões Estaduais',
            'bio' => 'Missionária e conferencista na área de capacitação familiar e ministério com mulheres.',
            'avatar_url' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=400&q=80',
            'social_links' => [
                'instagram' => 'https://instagram.com/cbngoias',
                'email' => 'missoes@cbngo.org.br',
            ],
        ]);

        // 3. Artigos e reflexões pastorais
        Article::create([
            'author_id' => $author1->id,
            'title' => 'Avivamento Bíblico e Missões no Século XXI',
            'slug' => 'avivamento-biblico-missoes-seculo-xxi',
            'category' => 'Missões',
            'excerpt' => 'Como a igreja contemporânea em Goiás pode manter a chama do Espírito Santo aliada à fidelidade doutrinária e ao compromisso evangelístico irrevogável.',
            'content' => "O verdadeiro avivamento não se resume a momentos passageiros de emoção, mas traduz-se em arrependimento genuíno, temor ao Senhor e paixão pelas almas perdidas.\n\nQuando o Espírito Santo opera de forma soberana em uma comunidade batista, Ele desperta crentes para a intercessão contínua, reconcilia relacionamentos rompidos e impulsiona a igreja a sair de suas quatro paredes rumo aos campos que já estão brancos para a ceifa.\n\nQue a CBN Goiás permaneça inabalável nesse compromisso com o Evangelho da graça.",
            'reading_time' => 6,
            'is_published' => true,
            'published_at' => now()->subDays(2),
        ]);

        Article::create([
            'author_id' => $author2->id,
            'title' => 'Pastoreamento e Saúde Emocional do Líder Cristão',
            'slug' => 'pastoreamento-saude-emocional-lider-cristao',
            'category' => 'Liderança',
            'excerpt' => 'Princípios indispensáveis para manter a integridade física, emocional e espiritual no exercício do ministério pastoral em tempos de sobrecarga.',
            'content' => "O apóstolo Paulo nos adverte com sabedoria: cuidai de vós mesmos e de todo o rebanho. Nenhum pastor pode oferecer aquilo que não possui em sua comunhão íntima com Cristo.\n\nEm um cenário de demandas ministeriais crescentes, a solidão do líder e o esgotamento silencioso têm atingido preciosos vocacionados. Cuidar da mente, estabelecer limites saudáveis e cultivar amizades transparentes no presbitério são imperativos de sobrevivência e fecundidade pastoral.",
            'reading_time' => 5,
            'is_published' => true,
            'published_at' => now()->subDays(5),
        ]);

        Article::create([
            'author_id' => $author3->id,
            'title' => 'A Centralidade de Cristo na Vida Familiar',
            'slug' => 'centralidade-de-cristo-vida-familiar',
            'category' => 'Família',
            'excerpt' => 'Edificando lares inabaláveis sobre os alicerces das Escrituras Sagradas, resgatando o culto doméstico e o discipulado de novas gerações.',
            'content' => "A família é o primeiro e mais importante campo missionário de todo discípulo de Jesus Cristo. Resgatar o altar doméstico é a prioridade urgente de nossas igrejas.\n\nQuando pais e mães ensinam com amor e testemunho prático os mandamentos de Deus, preparamos uma nova geração capaz de discernir a verdade em tempos de confusão cultural e moral.",
            'reading_time' => 4,
            'is_published' => true,
            'published_at' => now()->subDays(8),
        ]);

        // 4. Notícias e Comunicados Oficiais
        News::create([
            'title' => 'Diretoria Executiva divulga calendário oficial para o ano eclesiástico de 2026',
            'slug' => 'diretoria-divulga-calendario-oficial-2026',
            'category' => 'Comunicado Oficial',
            'excerpt' => 'A diretoria da Convenção Batista Nacional do Estado de Goiás reuniu-se na sede estadual para homologar as datas das assembleias e conferências.',
            'content' => 'Em reunião solene realizada na sede estadual da CBN Goiás, a diretoria executiva deliberou sobre as metas de expansão ministerial...',
            'church_name' => 'Sede Estadual CBN-GO',
            'city' => 'Goiânia',
            'is_official' => true,
            'is_published' => true,
            'published_at' => now()->subDays(1),
        ]);

        News::create([
            'title' => 'Nova congregação batista nacional é inaugurada com grande júbilo em Rio Verde',
            'slug' => 'nova-congregacao-inaugurada-rio-verde',
            'category' => 'Missões',
            'excerpt' => 'Com a presença de pastores de toda a região sudoeste de Goiás, os irmãos celebraram a abertura da nova frente missionária.',
            'content' => 'O avanço missionário em território goiano continua gerando frutos abençoados para a glória de Deus...',
            'church_name' => 'IBN Rio Verde',
            'city' => 'Rio Verde',
            'gallery_images' => [
                'https://images.unsplash.com/photo-1548625361-195972855734?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1438232992991-995b7058bbb3?auto=format&fit=crop&w=1200&q=80',
                'https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&w=1200&q=80',
            ],
            'is_official' => false,
            'is_published' => true,
            'published_at' => now()->subDays(3),
        ]);

        News::create([
            'title' => 'SETEBAN-GO abre processo seletivo para turmas de Teologia Ministerial 2026',
            'slug' => 'seteban-go-abre-processo-seletivo-2026',
            'category' => 'Educação Teológica',
            'excerpt' => 'O Seminário Teológico Batista Nacional em Goiás convida vocacionados e líderes locais para o vestibular do primeiro semestre.',
            'content' => 'O SETEBAN-GO reafirma seu compromisso de mais de quatro décadas na formação eclesiástica séria e confessional...',
            'church_name' => 'SETEBAN-GO',
            'city' => 'Goiânia',
            'is_official' => true,
            'is_published' => true,
            'published_at' => now()->subDays(4),
        ]);

        // 5. Congregações Filiadas à CBN Goiás (Guia e Diretório Estadual)
        Church::create([
            'name' => 'Catedral Batista Nacional de Goiânia',
            'slug' => 'catedral-batista-nacional-goiania',
            'pastor_name' => 'Pr. Roberto Silveira',
            'address' => 'Av. T-9, 2450',
            'neighborhood' => 'Jardim América',
            'city' => 'Goiânia',
            'state' => 'GO',
            'zip_code' => '74255-220',
            'latitude' => -16.7156000,
            'longitude' => -49.2834000,
            'phone' => '(62) 3251-4000 / (62) 99911-2026',
            'email' => 'secretaria@catedralcbngo.org.br',
            'social_links' => [
                'instagram' => 'https://instagram.com/cbngoias',
                'facebook' => 'https://facebook.com/cbngoias',
                'youtube' => 'https://youtube.com/@cbngoias',
            ],
            'service_times' => [
                ['day' => 'Domingo', 'time' => '09h00', 'label' => 'Escola Bíblica Dominical'],
                ['day' => 'Domingo', 'time' => '19h00', 'label' => 'Culto de Celebração'],
                ['day' => 'Quarta-feira', 'time' => '20h00', 'label' => 'Reunião de Oração e Palavra'],
            ],
            'is_active' => true,
        ]);

        Church::create([
            'name' => 'Igreja Batista Nacional Betel',
            'slug' => 'igreja-batista-nacional-betel-goiania',
            'pastor_name' => 'Pr. Marcos Vinícius Rocha',
            'address' => 'Rua T-37, 1120',
            'neighborhood' => 'Setor Bueno',
            'city' => 'Goiânia',
            'state' => 'GO',
            'zip_code' => '74230-025',
            'latitude' => -16.7032000,
            'longitude' => -49.2665000,
            'phone' => '(62) 3285-1900',
            'email' => 'contato@ibnbetelgo.org.br',
            'social_links' => [
                'instagram' => 'https://instagram.com/cbngoias',
            ],
            'service_times' => [
                ['day' => 'Domingo', 'time' => '18h30', 'label' => 'Culto da Família'],
                ['day' => 'Quinta-feira', 'time' => '20h00', 'label' => 'Noite de Intercessão'],
            ],
            'is_active' => true,
        ]);

        Church::create([
            'name' => 'Primeira Igreja Batista Nacional de Anápolis',
            'slug' => 'primeira-ibn-anapolis',
            'pastor_name' => 'Pr. Carlos Eduardo Mendes',
            'address' => 'Rua Manoel D\'Abadia, 410',
            'neighborhood' => 'Bairro Jundiaí',
            'city' => 'Anápolis',
            'state' => 'GO',
            'zip_code' => '75110-420',
            'latitude' => -16.3285000,
            'longitude' => -48.9534000,
            'phone' => '(62) 3324-5500',
            'email' => 'secretaria@pibnanapolis.org.br',
            'social_links' => [
                'instagram' => 'https://instagram.com/cbngoias',
                'youtube' => 'https://youtube.com/@cbngoias',
            ],
            'service_times' => [
                ['day' => 'Domingo', 'time' => '09h00', 'label' => 'Estudo Bíblico'],
                ['day' => 'Domingo', 'time' => '19h00', 'label' => 'Culto de Adoração'],
                ['day' => 'Terça-feira', 'time' => '19h30', 'label' => 'Tarde e Noite da Bênção (Oração)'],
            ],
            'is_active' => true,
        ]);

        Church::create([
            'name' => 'Igreja Batista Nacional de Rio Verde',
            'slug' => 'ibn-rio-verde',
            'pastor_name' => 'Pr. Samuel Costa',
            'address' => 'Av. Presidente Vargas, 1840',
            'neighborhood' => 'Jardim Goiás',
            'city' => 'Rio Verde',
            'state' => 'GO',
            'zip_code' => '75903-290',
            'latitude' => -17.7923000,
            'longitude' => -50.9192000,
            'phone' => '(64) 3621-3344',
            'email' => 'contato@ibnrioverde.org.br',
            'social_links' => [
                'instagram' => 'https://instagram.com/cbngoias',
            ],
            'service_times' => [
                ['day' => 'Domingo', 'time' => '19h00', 'label' => 'Culto de Celebração'],
                ['day' => 'Quarta-feira', 'time' => '19h30', 'label' => 'Culto de Oração e Doutrina'],
            ],
            'is_active' => true,
        ]);

        Church::create([
            'name' => 'Igreja Batista Nacional Filadélfia de Aparecida',
            'slug' => 'ibn-filadelfia-aparecida',
            'pastor_name' => 'Pr. Lucas Almeida',
            'address' => 'Av. Independência, Qd. 14, Lt. 08',
            'neighborhood' => 'Cidade Livre',
            'city' => 'Aparecida de Goiânia',
            'state' => 'GO',
            'zip_code' => '74963-100',
            'latitude' => -16.8233000,
            'longitude' => -49.2439000,
            'phone' => '(62) 3283-7788',
            'email' => 'filadelfia@cbngo.org.br',
            'social_links' => [
                'instagram' => 'https://instagram.com/cbngoias',
                'facebook' => 'https://facebook.com/cbngoias',
            ],
            'service_times' => [
                ['day' => 'Domingo', 'time' => '18h00', 'label' => 'Culto de Louvor e Adoração'],
                ['day' => 'Quinta-feira', 'time' => '19h30', 'label' => 'Reunião de Oração'],
            ],
            'is_active' => true,
        ]);

        Church::create([
            'name' => 'Igreja Batista Nacional Monte Horebe de Jataí',
            'slug' => 'ibn-monte-horebe-jatai',
            'pastor_name' => 'Pr. Ezequiel Tavares',
            'address' => 'Rua Miranda de Carvalho, 915',
            'neighborhood' => 'Setor Central',
            'city' => 'Jataí',
            'state' => 'GO',
            'zip_code' => '75800-055',
            'latitude' => -17.8814000,
            'longitude' => -51.7144000,
            'phone' => '(64) 3631-8090',
            'email' => 'jatai@cbngo.org.br',
            'social_links' => [
                'instagram' => 'https://instagram.com/cbngoias',
            ],
            'service_times' => [
                ['day' => 'Domingo', 'time' => '19h00', 'label' => 'Culto Dominical'],
                ['day' => 'Quarta-feira', 'time' => '19h30', 'label' => 'Reunião de Oração'],
            ],
            'is_active' => true,
        ]);

        // 6. Diretoria e Órgãos da Convenção (rascunho — nomes a confirmar com a secretaria)
        $boardMembers = [
            ['name' => 'Pr. Roberto Silveira', 'role' => 'Presidente', 'group' => 'presidencia', 'photo_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=400&q=80'],
            ['name' => 'Pr. Carlos Eduardo Mendes', 'role' => '1º Vice-presidente', 'group' => 'vice_presidencia', 'position' => 1, 'photo_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=400&q=80'],
            ['name' => 'Pr. André Luiz Barbosa', 'role' => '2º Vice-presidente', 'group' => 'vice_presidencia', 'position' => 2],
            ['name' => 'Pra. Helena Albuquerque', 'role' => '1ª Secretária', 'group' => 'secretaria', 'position' => 1, 'photo_url' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=400&q=80'],
            ['name' => 'Pr. Daniel Ferreira Costa', 'role' => '2º Secretário', 'group' => 'secretaria', 'position' => 2],
            ['name' => 'Diác. Márcio Antunes', 'role' => '1º Tesoureiro', 'group' => 'tesouraria', 'position' => 1],
            ['name' => 'Presb. Juliana Rezende', 'role' => '2ª Tesoureira', 'group' => 'tesouraria', 'position' => 2],
            ['name' => 'Pr. Valdir Cunha', 'role' => 'Relator', 'group' => 'conselho_fiscal', 'position' => 1],
            ['name' => 'Pr. Samuel Rocha', 'role' => 'Vogal', 'group' => 'conselho_fiscal', 'position' => 2],
            ['name' => 'Pr. Elias Moreira', 'role' => 'Vogal', 'group' => 'conselho_fiscal', 'position' => 3],
            ['name' => 'Pr. Fernando Lopes', 'role' => 'Coordenador Regional', 'group' => 'coordenacao_regional', 'region' => 'Região Metropolitana de Goiânia', 'position' => 1],
            ['name' => 'Pr. Gilberto Nunes', 'role' => 'Coordenador Regional', 'group' => 'coordenacao_regional', 'region' => 'Região Centro (Anápolis)', 'position' => 2],
            ['name' => 'Pr. Wesley Prado', 'role' => 'Coordenador Regional', 'group' => 'coordenacao_regional', 'region' => 'Região Sudoeste (Rio Verde e Jataí)', 'position' => 3],
            ['name' => 'Pr. Josué Teixeira', 'role' => 'Coordenador Regional', 'group' => 'coordenacao_regional', 'region' => 'Região Entorno do DF (Luziânia)', 'position' => 4],
        ];

        foreach ($boardMembers as $member) {
            BoardMember::create($member + ['term_label' => 'Gestão 2025–2027', 'is_active' => true]);
        }

        // 7. Documentos oficiais para consulta/download
        Document::create([
            'title' => 'Estatuto da CBN',
            'slug' => 'estatuto-da-cbn',
            'description' => 'Estatuto social vigente da Convenção Batista Nacional: finalidades, órgãos deliberativos e administrativos, recursos e reforma estatutária.',
            'category' => 'estatuto',
            'file_url' => 'https://cbngo.com.br/dados-abertos/estatuto.pdf',
            'file_format' => 'PDF',
            'position' => 1,
            'is_active' => true,
        ]);

        Document::create([
            'title' => 'Regimento Interno da CBN',
            'slug' => 'regimento-interno-da-cbn',
            'description' => 'Normas de filiação, direitos e deveres das igrejas, funcionamento da Assembleia Geral, do Conplex e das convenções estaduais.',
            'category' => 'regimento',
            'file_url' => 'https://cbngo.com.br/dados-abertos/regimento-interno.pdf',
            'file_format' => 'PDF',
            'position' => 2,
            'is_active' => true,
        ]);

        Document::create([
            'title' => 'Pacto Renovado',
            'slug' => 'pacto-renovado',
            'description' => 'Pacto firmado pelos batistas nacionais em Luziânia-GO: sonho, visão, perdão, conduta, compromissos administrativos e futuro.',
            'category' => 'pacto',
            'file_url' => 'https://cbngo.com.br/pacto-renovado/',
            'file_format' => 'Página',
            'position' => 3,
            'is_active' => true,
        ]);
    }
}
