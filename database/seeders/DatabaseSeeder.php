<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Author;
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
    }
}
