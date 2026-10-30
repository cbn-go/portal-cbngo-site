# Especificação Técnica (Spec) — Issue #37: Contatos, redes sociais e endereço lidos do painel

- **Issue:** [#37 — [Integração] Contatos, redes sociais e endereço lidos do painel](https://github.com/cbn-go/portal-cbngo-site/issues/37)
- **Painel:** [admin#44 — formulário de dados institucionais](https://github.com/cbn-go/portal-cbngo-admin/issues/44)
- **Fase WDLC:** Integração Painel ↔ Portal Público
- **Metodologia:** Spec-Driven Development (SDD) com ciclo TDD Mandatório
- **Data:** 30 de Outubro de 2026
- **Status:** Aprovada

---

## 1. Objetivo & Escopo

Os dados de contato, o endereço da sede e as redes sociais passam a ser mantidos pela secretaria no painel (Sistema → Dados institucionais, tabela `institutional_settings`). O site lê o registro do banco compartilhado. `config/cbngo.php` fica só como fallback.

### 1.1 Goals
- Fonte única `App\Support\InstitutionalInfo`, usada pela barra superior, pelo rodapé, por `/contato` e pelo JSON-LD `Organization`.
- Cache com chave versionada pelo `updated_at` do registro: ao salvar no painel, o site exibe os dados novos na hora.
- Fallback para o config quando a tabela estiver vazia ou indisponível. O site nunca quebra.
- Só as redes preenchidas aparecem (Instagram, Facebook, YouTube, TikTok, Spotify e LinkedIn), cada uma com ícone e `aria-label`.
- Endereço completo no rodapé e em `/contato`. "Como chegar" usa o `maps_url` do painel ou, sem ele, a busca do Google Maps com o endereço montado.
- `PostalAddress` completo no schema.org, com `sameAs` só das redes preenchidas.

### 1.2 Non-Goals
- Edição dos dados, que é feita no painel.
- Mudança visual além dos ícones das redes novas e da linha de telefone no rodapé.

### 1.3 Discovery
Só estes pontos exibem contato, endereço ou redes. Nenhuma outra view tem e-mail ou endereço fixo.

| Local | Arquivo |
|---|---|
| Barra superior (e-mail e redes) | `resources/views/layouts/app.blade.php` |
| Rodapé (redes, WhatsApp, endereço, telefone, e-mail e horário) | `resources/views/components/footer.blade.php` |
| `/contato` | `InstitutionalController::contact()` e `resources/views/institucional/contato.blade.php` |
| JSON-LD `Organization` | `App\Support\StructuredData::organization()` |

---

## 2. Contratos

### 2.1 Tabela `institutional_settings` (espelho do painel, registro único)
- **Contato:** `email` (obrigatório), `phone`, `whatsapp` e `office_hours`.
- **Endereço:** `street`, `number`, `complement`, `district`, `city` (padrão Goiânia), `state` (padrão GO), `postal_code` e `maps_url`.
- **Redes:** `instagram_url`, `facebook_url`, `youtube_url`, `tiktok_url`, `spotify_url` e `linkedin_url` (vazio = rede oculta).
- **Auditoria:** `updated_by` e timestamps.

### 2.2 `App\Support\InstitutionalInfo`
- `load()`:
  - lê a versão (`max(updated_at)` e `count(*)`) e guarda os dados em `Cache::remember('institucional.'.md5(versão), config('cbngo.institutional.cache_ttl'))`;
  - sem registro, ou se o banco falhar, usa `config('cbngo.contact')` e `config('cbngo.social')`.
- Registrado como `scoped` no container: uma carga por request. Um view composer injeta `$institutional` em `layouts.app` e `components.footer`.
- **Contato:** `email()`, `phone()`, `whatsapp()`, `officeHours()`, `phoneHref()` e `whatsappUrl()`, os dois últimos via `ContactLinks`.
- **Endereço:**
  - `address()`: linha única, com o formato `Rua 04, 515, 12º andar… - Setor Central, Goiânia - GO, CEP 74020-045`;
  - `addressLines()`: a mesma informação em linhas;
  - `postalAddress()`: o array do schema.org;
  - `mapsUrl()`: o link do "Como chegar".
- **Redes:** `socialLinks()` devolve `array<string, array{url: string, label: string}>` só com as redes preenchidas, na ordem fixa.

### 2.3 Fallback (`config/cbngo.php`)
- `contact`: as mesmas chaves da tabela, com o endereço oficial do Art. 2º do Estatuto (Rua 04, 515, 12º andar, sala 1.207, Edifício Parthenon Center, Setor Central, Goiânia-GO, CEP 74020-045).
- `social`: as redes, indexadas por rede.
- `institutional.cache_ttl`: padrão 3600 s. A chave muda a cada edição; o TTL só limita o tamanho do cache.

---

## 3. Testes (`tests/Feature/InstitutionalInfoTest.php`)
1. Com registro, a barra superior, o rodapé e `/contato` mostram os dados do banco: e-mail, `tel:`, `wa.me`, endereço completo e `maps_url`.
2. Sem `maps_url`, "Como chegar" usa a busca do Google Maps com o endereço montado.
3. Sem registro, os dados vêm do fallback do config.
4. Com a tabela indisponível, as páginas respondem 200 com o fallback.
5. Uma rede vazia não aparece. TikTok preenchido aparece com `aria-label`.
6. O JSON-LD traz o `PostalAddress` completo e `sameAs` só com as redes preenchidas.
7. Uma edição no registro (`updated_at`) invalida o cache.

---

## 4. Launch
Depois do seed do painel em produção, conferir com a secretaria o rodapé, `/contato` e o JSON-LD.
