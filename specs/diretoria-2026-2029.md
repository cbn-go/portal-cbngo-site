# Especificação Técnica (Spec) — Issue #45: /diretoria com CONPLEX, títulos e regionais AMIGO

- **Issue:** [#45 — [Integração] /diretoria: exibir CONPLEX, títulos e regionais AMIGO por região](https://github.com/cbn-go/portal-cbngo-site/issues/45)
- **Origem:** [cbn-go/portal-cbngo-admin#62](https://github.com/cbn-go/portal-cbngo-admin/pull/62) — estrutura e composição da gestão 2026–2029
- **Base:** `specs/modulo-paginas-institucionais.md` (Sub-issue 7.2)
- **Metodologia:** Spec-Driven Development (SDD) com ciclo TDD Mandatório
- **Data:** 30 de Outubro de 2026
- **Status:** Aprovada

---

## 1. Objetivo & Escopo

O painel cadastrou a diretoria eleita na XXVI Assembleia Geral (triênio 2026–2029) e mudou o contrato de `board_members`. O site precisa exibir todos os 30 membros ativos.

### 1.1 Goals
- **D.1 — CONPLEX:**
  - grupo `conplex` = "Conselho de Planejamento e Execução (CONPLEX)", entre Tesouraria e Conselho Fiscal;
  - a ordem completa é Presidência, Vice-presidência, Secretaria, Tesouraria, CONPLEX, Conselho Fiscal e Diretorias Regionais (AMIGO).
- **D.2 — Títulos:**
  - o nome é exibido como `{title} {name}` (`BoardMember::getDisplayName()`), por exemplo "Pr. André Luiz Rodrigues da Silva";
  - sem título, o membro é leigo e só o `name` aparece;
  - as iniciais do avatar vêm só do `name`.
- **D.3 — Regionais AMIGO:**
  - o grupo `coordenacao_regional` passa a se chamar "Diretorias Regionais (AMIGO)";
  - os membros são agrupados por `region`, na ordem `leste`, `oeste`, `norte` e `sudoeste`, com os subtítulos "AMIGO Leste" etc. (`BoardMember::REGIONS`, `data-board-region="{chave}"`);
  - o selo de região sai do card.
- **D.4 — Nenhum membro some em silêncio:**
  - **grupo desconhecido:** vai para o fim, em "Outros membros da diretoria" (`data-board-group="{chave}"`);
  - **região vazia ou fora das quatro chaves:** vai para um subgrupo no fim da seção regional, rotulado com o texto original ou com "Sem região definida";
  - os dois casos registram um `Log::warning`.
- **D.5 — Dados:**
  - **migration de espelho:** inclui `title`, `term_starts_on` e `term_ends_on`;
  - **`DatabaseSeeder`:** traz os 30 membros reais do triênio, os mesmos do `BoardTerm20262029Seeder` do painel;
  - **factory:** passa a usar `name` sem título.

### 1.2 Non-Goals
- **Filtro por mandato:** a página continua exibindo só `is_active = 1`. O painel já desativa as gestões anteriores, e os campos `term_starts_on` e `term_ends_on` não filtram nada.
- **Conversão de dados antigos:** a separação do título e a normalização das regiões são feitas pela migration do painel.

---

## 2. Contrato (`board_members`)

| Coluna | Uso no site |
|---|---|
| `title` | Prefixo do nome exibido; nulo = leigo |
| `name` | Nome sem título; base das iniciais |
| `group` | Chave de `BoardMember::GROUPS`; desconhecida = "Outros membros da diretoria" |
| `region` | Só em `coordenacao_regional`: `leste`, `oeste`, `norte` ou `sudoeste` |
| `term_label` | Exibido no card ("Triênio 2026–2029") |
| `term_starts_on`, `term_ends_on` | Mapeados no model; não são exibidos |

---

## 3. Critério de aceite

Com o `DatabaseSeeder`, `/diretoria` mostra os 30 membros ativos, com os títulos antes dos nomes:

| Grupo | Membros |
|---|---|
| Presidência | 1 |
| Vice-presidência | 1 |
| Secretaria | 2 |
| CONPLEX | 4 |
| Conselho Fiscal | 6 |
| Diretorias Regionais | 16, 4 por AMIGO |

## 4. Plano de testes (`tests/Feature/InstitutionalPagesTest.php`)
1. `test_board_page_groups_members_in_institutional_order()`: ordem dos órgãos, incluindo o CONPLEX, e os rótulos novos.
2. `test_board_member_card_shows_title_before_name()`: título no card e no alt, membro leigo sem prefixo e iniciais sem o título.
3. `test_board_page_groups_regional_members_by_amigo_region()`: ordem das regiões, subtítulos, `position` e região desconhecida no fim.
4. `test_board_page_shows_unknown_groups_with_generic_label_and_logs()`.
5. `test_board_page_hides_inactive_members()`.
6. `test_database_seeder_populates_current_board_and_documents()`: os 30 membros e as contagens por grupo e por região.
