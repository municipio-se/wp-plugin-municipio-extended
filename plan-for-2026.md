# Plan for LTS 2026

Nuvarande LTS-head: `f3a2e19`. Aktuell pluginversion: `2025.12.13`.
Jämförelseyta för 2026: `wp-theme-municipio` `7.7.18` med inbäddad
`Modularity/`, `wp-plugin-hbg-component-library` `5.23.1`,
`wp-plugin-hbg-event-manager-integration` `3.1.4` och
`wp-plugin-modularity-form-builder` `4.0.12`.

## Slutsats

Municipio Extended bör behandlas som ett paket av LTS-specifika features, inte
som en vanlig upstream-rebas. Flera delar är fortfarande tydliga lokala värden,
men de största ytorna går rakt in i funktioner som Helsingborg nu har byggt om i
temat, den inbäddade Modularity-koden och komponentbiblioteket.

Första passet pekar på att arbetet bör delas per featuregrupp. Modularity-,
MXUI-/komponent-, eventformulär-, theme-mod- och sökdelarna kräver djupare
jämförelse innan något porteras. Mindre säkerhets-, admin- och WordPress-fixar
kan sannolikt behållas smalt om de fortfarande behövs i LTS.

## Arbetsplan

- [ ] Gör en funktionsinventering per filgrupp i Extended och markera varje
      feature som `behåll`, `ersätt med upstream`, `porta smalt`, `släpp` eller
      `oklart`.
- [ ] Jämför alla Modularity-relaterade features mot
      `wp-theme-municipio/Modularity/`, inte mot den gamla separata pluginen.
- [ ] Uppdatera Composer-strategin när temat blir ägare av Modularity och
      Extended fortfarande kräver `municipio/wp-plugin-modularity`.
- [ ] Verifiera vilka `mx_*`- och `Modularity/...`-hooks som är verkliga
      integrationskontrakt innan de återskapas.
- [ ] Jämför MXUI-komponentöverlagringen mot upstreams nya styleguide, modifiers
      och Material Symbols-stöd.
- [ ] Jämför sökimplementationen mot tema, ElasticPress och faktisk LTS-sök-UX
      innan den behålls som pluginansvar.
- [ ] Jämför eventformulärsfälten mot `wp-plugin-hbg-event-manager-integration`
      `3.1.4`.
- [ ] Flytta eller ta bort features som hör hemma i tema eller annat paket när
      ägarskapet är tydligt.
- [ ] Låt byggda assets och språkfiler uppdateras av ordinarie verktyg efter
      implementation.

## Beslutstabell

| Område                                                  | Extended-yta                                                                                                                            | Helsingborg-läge                                                                                                        | Bedömning                                                                           |
| ------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------- |
| Composer och paketering                                 | Pluginen kräver bland annat `municipio/wp-plugin-modularity`, ElasticPress-klient och LTS-relaterade dev-beroenden.                     | `wp-theme-municipio` `7.7.18` autoloadar `Modularity\\` direkt från temat.                                              | Återskapa smalare; lös särskilt Modularity-kravet innan 2026-bundlet.               |
| Bootstrap och autoload                                  | `municipio-extended.php` laddar alla `autoload/*.php`, stödjer MU-läge och använder textdomain `municipio-extended`.                    | Ingen direkt upstream-motsvarighet, men flera funktioner har flyttat till tema eller andra plugins.                     | Behåll plugin-bootstrap tills features har flyttats eller släppts.                  |
| Sök och ElasticPress                                    | Egen AJAX-sökning, Elasticsearch-query, boosting, decay, highlights, `external_page`, sökord och fel-loggning.                          | Ingen tydlig motsvarighet hittades i tema `7.7.18`, men tema/ElasticPress påverkar samma sökflöden.                     | Behåll som kandidat; djupgranska mot faktisk sök-UX och indexstrategi.              |
| Async jobs                                              | CPT `async_job`, AJAX-trigger, cron och handlerregister via `mx_register_async_job_handler`.                                            | Ingen tydlig motsvarighet hittades i jämförelseytan.                                                                    | Behåll eller ersätt med standardiserad jobblösning om sådan finns i LTS.            |
| Inbäddad Modularity                                     | Extended ändrar templates, wrapper-attribut, grupper, editor UI, sidebarbeteenden och modulspecifika view data.                         | Modularity ligger i temat och har ny service-, asset- och modulstruktur.                                                | Djupgranska först; porta inte gamla pluginantaganden rakt av.                       |
| `mod-navigation`                                        | Egen navigationsmodul med barn/syskon/manuell/meny, Nested Pages-stöd och headless current-post-hantering.                              | Temat har nyare navigation, menyer och Modularity-struktur, men ingen uppenbar en-till-en-modul hittades i snabbpasset. | Oklart; jämför datamodell och faktisk kundanvändning.                               |
| `mod-posts` och filtrering                              | Mixed template, taxonomival, arkivtabell, queryfilter, datakällor och wrapperdata.                                                      | Temats Modularity har ny Posts-arkitektur med schema, multisite, pagination och fler controllers.                       | Hög krockrisk; ersätt med upstream där möjligt och återskapa bara saknade kontrakt. |
| ManualInput, Text och segment                           | Segmenttemplates, färgpresets, item-/templatefilter och designlager ovanpå Modularity.                                                  | Temat har nyare ManualInput- och Text-stöd men saknar sannolikt delar av LTS-kontrakten.                                | Jämför per feature; behåll bara synligt eller integrerat beteende.                  |
| Video, Iframe, Timeline, FilesList, Contacts och Notice | Extra modultyper/templates, Mediaflow-/embedhantering och komponentdata.                                                                | Temat har flera av dessa moduler inbyggda eller ombyggda i Modularity.                                                  | Jämför modul för modul; släpp dubblering där upstream täcker.                       |
| MXUI och komponentöverlagring                           | Egna controllers, view paths, modifiers och komponentanpassningar för card, segment, nav, icon, datebadge, image, modal och timeline.   | Komponentbibliotek `5.23.1` har ny modifier-yta, Material Symbols-stöd och sanitizer-kontrakt.                          | Hög krockrisk; behandla som design-/kompatibilitetslager och verifiera manuellt.    |
| Material Symbols och ikoner                             | Lokal cache, SVG-URL:er, helperfunktioner och ikonlistor.                                                                               | Temat har inline Material Symbols CSS och komponentbiblioteket har Material Symbols-hantering.                          | Troligen ersätt med upstream; behåll bara om LTS behöver cache- eller URL-kontrakt. |
| Uppladdade typsnitt                                     | Egna MIME- och Kirki-fontintegrationer.                                                                                                 | Temat har migration mot WordPress native Font Library.                                                                  | Ersätt med upstreams fontstrategi om LTS går till `7.7.18`.                         |
| Theme/customizer-inställningar                          | Layout, header, typografi, print, MXUI-färger, fallbackbilder, arkivtabeller och modulgruppsbakgrunder.                                 | Temat `7.7.18` har ombyggd Customizer och design-/theme-mod-hantering.                                                  | Mappa theme_mod-nycklar; porta bara inställningar som saknar upstream-motsvarighet. |
| Theme mods export/import/clone                          | Adminverktyg för export, import och kloning av theme mods.                                                                              | Temat har `LoadDesign` och relaterad theme-mod-lagring.                                                                 | Ersätt eller slå ihop med upstreams designimport.                                   |
| Error pages                                             | Egen 404-hantering och rendering.                                                                                                       | Temat `7.7.18` har `E404`-controller och Customizer-sektion för felsidor.                                               | Släpp eller ersätt med tema.                                                        |
| Admin- och säkerhetsdefaults                            | Döljer adminytor, stänger metadata-plugin, stänger avancerad HTML, stänger custom code och förenklar editoråtkomst.                     | Temat saknar fortfarande vissa LTS-säkerhetsdefaults enligt tidigare grep, särskilt custom code.                        | Behåll/återskapa smalt där beslutet fortfarande gäller.                             |
| WordPress- och nätverksfixar                            | User enumeration, `wp_img_tag_add_auto_sizes`, attachment URL-cache, robots upload blocking, rewrite-normalisering och e-postavsändare. | Spridda eller saknade upstream-motsvarigheter i snabbpasset.                                                            | Behåll selektivt efter verifiering; separera från design-/modulfeatures.            |
| Event Manager Integration                               | Anpassar event hero, taxonomirewrite och ersätter EventForm-fält med datalist/API-drivna alternativ.                                    | Integration `3.1.4` har EventForm och `cleanHero`, men inte uppenbart samma fältlogik.                                  | Djupgranska mot integrationens plan; hög risk för dubblering.                       |
| Modularity Form Builder                                 | Form- och fileupload-relaterade justeringar finns i Extendeds assets/autoload.                                                          | Form Builder `4.0.12` har omfattande filuppladdning, kryptering och submission-flöden.                                  | Jämför specifika fixar; flytta inte formulärlogik utan ägarskapsbeslut.             |
| Migreringar och adminverktyg                            | Migrations-UI, aktivitetslogg, 2FA, Redirection-åtkomst, RSS-prenumeration och GDPR/tracking-glue.                                      | Ingen tydlig upstream-motsvarighet i första passet.                                                                     | Behåll om de är LTS-affärsregler; överväg senare uppdelning.                        |

## Första djupgranskningspass

1. Modularity och Composer: avgör hur Extended ska fungera när Modularity inte
   längre är en separat plugin i 2026-basen.
2. `mod-posts`, `mod-navigation`, ManualInput/Text och övriga moduler: jämför
   Extendeds fält, templates, hooks och view data mot temats `Modularity/`.
3. MXUI och komponenter: jämför Extendeds component overrides mot
   komponentbibliotek `5.23.1` och temats nya styleguide.
4. Sök: avgör om `mx_search` fortsatt ska vara en LTS-feature eller flyttas
   närmare tema/ElasticPress-konfiguration.
5. Eventformulär: jämför fältmodellen mot
   `wp-plugin-hbg-event-manager-integration` innan någon portning görs.
6. Säkerhets- och adminfixar: bryt ut de tydliga policybesluten från större
   UX-/designfeatures så att de kan porteras smalt.

## Risker att verifiera

- Extended har många publika `mx_*`, `Modularity/...`, `ComponentLibrary/...`
  och `EventManagerIntegration/...`-hooks som kan vara kundkontrakt trots att de
  saknar upstream-motsvarighet.
- Samma feature kan nu finnas i två lager: Extended och temats inbäddade
  Modularity. Det gäller särskilt moduler, templates, assets och editorbeteende.
- Komponentöverlagringen kan krocka med upstreams nya sanitizer-, modifier- och
  Material Symbols-kontrakt.
- Sökflödet går förbi ElasticPress query integration och behöver
  regressionsgranskas innan det behålls.
- Theme_mod- och Customizer-nycklar kan ha bytt betydelse mellan LTS 2025 och
  temat `7.7.18`.
- Eventformulär och Form Builder kan dela ansvarsområden runt filer, datalistor,
  validering och API-anrop.

## Analyskommandon

- `git rev-parse --short HEAD`
- `git log --oneline --max-count=20`
- `rg --files`
- Riktade `rg`-sökningar efter `add_action`, `add_filter`, `register_post_type`,
  `acf_add_local_field_group`, `wp_ajax`, `mx_`, `Modularity/`, `Municipio/`,
  `ComponentLibrary/` och `EventManagerIntegration/`.
- Riktade `git grep`-sökningar i `wp-theme-municipio` `7.7.18`,
  `wp-plugin-hbg-component-library` `5.23.1`,
  `wp-plugin-hbg-event-manager-integration` `3.1.4` och
  `wp-plugin-modularity-form-builder` `4.0.12`.
