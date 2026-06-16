# Plan for LTS 2026

Nuvarande LTS-head före planarbete: `f3a2e19`. Aktuell pluginversion:
`2025.12.13`. Jämförelseyta för 2026: `wp-theme-municipio` `7.7.18` med inbäddad
`Modularity/`, `wp-plugin-hbg-component-library` `5.23.1`,
`wp-plugin-hbg-event-manager-integration` `3.1.4` och
`wp-plugin-modularity-form-builder` `4.0.12`.

## Slutsats

Municipio Extended bör inte rebasas som en traditionell fork. Det är ett
LTS-samlingsplugin med features som numera korsar flera nya Helsingborg-paket.
Rätt arbetssätt för 2026 är därför att bryta ner pluginen per ansvarsyta och
flytta, återskapa eller släppa varje feature där den hör hemma.

Det djupare passet pekar på tre huvudbeslut:

- Sök, `mod-navigation`, async jobs och vissa admin-/säkerhetspolicies har ingen
  tydlig upstreammotsvarighet och är kandidater att behålla i Extended eller
  porta smalt.
- `mod-posts`, ManualInput/Text, wrapper/editor-logik och större Modularity-
  hooks ska skrivas om mot temats inbäddade `Modularity/`, inte flyttas rakt.
- MXUI-/komponentöverlagring, eventformulär, theme mods, 404, fonts och Material
  Symbols ska jämföras mot nya upstreamlösningar och reduceras kraftigt.

## Analysmodell

Skillens normala A/B/C-modell är anpassad här eftersom Extended saknar en
enskild ny upstream-bas:

- `A`: pluginens äldre featurehistoria i
  `municipio-lts/wp-plugin-municipio-extended-2024`.
- `B`: LTS-head `f3a2e19`.
- `C`: de nya baspaketen ovan, främst `wp-theme-municipio` `7.7.18` eftersom
  temat nu äger `Modularity/`.

## Rekommenderad rebase-plan

1. Behåll Extended som ett tunt compatibility-/featureplugin tills varje feature
   har fått ett nytt ägarskap.
2. Ta först bort Composer-antagandet att separat Modularity-plugin är basen,
   eller ersätt det med en explicit 2026-strategi mot temats inbäddade
   `Modularity/`.
3. Gör `mod-navigation`, `mx_search` och async jobs till separata beslut. De har
   inte tydliga upstreamersättare.
4. Flytta Modularity-patchar till temats `Modularity/` bara där det är ett
   stabilt LTS-kontrakt eller ett saknat beteende.
5. Ersätt `mod-posts`-patchar med temats nya Posts-API:
   `Modularity/Module/Posts/GetPosts/Args`, `Modularity/Module/Posts/template`,
   `Modularity/Module/Posts/ArchiveLink/Icon` och nya view data.
6. Bryt ner MXUI till en lista över konkreta saknade designbeteenden. Återskapa
   inte hela `psr-4/ComponentLibrary/Component/*` som standard.
7. Låt eventformulärslogiken bo nära `wp-plugin-hbg-event-manager-integration`
   om den fortfarande behövs, eftersom upstream redan äger fältträdet och
   filterpunkten.
8. Släpp 404, fonts, Material Symbols och theme mods-import där temat redan har
   en motsvarande modern lösning.
9. Behåll säkerhets- och policybeslut smalt: custom code off by default,
   avancerad HTML off, user enumeration och eventuellt editor/adminförenkling.
10. Kör runtimeverifiering efter implementation för sök, navigation,
    eventformulär, Form Builder-filuppladdning och komponentrendering.

## Beslutstabell

| Område                                             | Vår slutändring                                                                                                                                      | Upstream-läge                                                                                                                                                                                                | Bedömning                                                     | Berörda commits                                                                                              |
| -------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ | ------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------ |
| Composer och paketering                            | Paketet heter `municipio/wp-plugin-municipio-extended` och kräver bland annat `municipio/wp-plugin-modularity`, ElasticPress-klient och DiDom.       | Tema `7.7.18` autoloadar `Modularity\\` direkt från temat. Det finns ingen separat 2026-bas för Extended som motsvarar ett upstreamplugin.                                                                   | Återskapa smalare                                             | `6e56935`, release-/docscommits                                                                              |
| Bootstrap och autoload                             | Pluginen laddar alla `autoload/*.php`, stödjer MU-läge och laddar textdomain tidigt.                                                                 | Flera features finns nu i tema eller andra plugins, men ingen gemensam bootstrap ersätter Extended.                                                                                                          | Behåll tills features flyttats                                | `96b4223`, `ff3c81e`                                                                                         |
| Modularity-ägarskap                                | Extended registrerar egna moduler, view paths och controller paths mot separat Modularity.                                                           | `wp-theme-municipio` `7.7.18` innehåller `Modularity/` med ny service-, asset-, hook- och modulstruktur.                                                                                                     | Återskapa smalare                                             | `da43245`, `ee86a0f`, `93dba43`                                                                              |
| Modulwrapper, grupper och editor                   | Lägger `mx/module_wrapper_attrs`, modulgrupper med bakgrund, editor-UI och sidebar-klasser.                                                          | Temats `Display` har kvar `Modularity/Display/BeforeModule`, `BeforeModule::classes`, `Markup` och sidebarlogik, men saknar modulgrupper och `pre_outputModule`.                                             | Återskapa smalare i tema om feature används                   | `8601c7e`, `849afff`, `e1b04e8`, `2df8197`, `ee88246`                                                        |
| `mod-navigation`                                   | Egen modul för barn, syskon, meny, manuell lista, Nested Pages, tomt-läge, aktuellt post-kontextfilter och flera format.                             | Temats `mod-menu` renderar valda menyer, men täcker inte barn/syskon/Nested Pages/current-post-logiken. Navigation i temat har egna page-tree helpers men inte samma modulkontrakt.                          | Behåll eller återskapa smalare                                | `125f677`, `3850e5f`, `a471928`, `2190ccc`, `3bda5f3`, `9de2934`, `bdb31c2`, `74b9eb7`, `e603baf`, `f35b2b1` |
| `mod-posts` mixed och taxonomier                   | Lägger mixed display, tar bort vissa visningslägen, lägger taxonomy display i kort, dataattribut och extra postObject-beteenden.                     | Temats Posts-modul har ny schema-/multisite-/pagination-/private-arkitektur och nya filter (`GetPosts/Args`, `template`, `ArchiveLink/Icon`). Gamla `Helper/getPosts/data`-ytan är inte rätt portningspunkt. | Ersätt med upstream och återskapa saknade kontrakt            | `9038ef4`, `52ea6db`, `5f47be6`, `1ea95bd`, `2c63e50`                                                        |
| Posts archive/rewrite och standardposttyp          | Extended hanterar `/nyheter`, inaktiverad standardposttyp, breadcrumb och rewrite-/archivejusteringar.                                               | Tema och inbäddad Modularity har ny arkivhantering, archive modules och Polylang-arkivlänkar.                                                                                                                | Verifiera manuellt                                            | `4bae846`, `eacc5aa`, `ac29ea0`, `770a9e7`                                                                   |
| ManualInput och Text                               | Lägger image ratio för ManualInput, textmodulens färgpresets, proseWrap och klassersättning.                                                         | Temats ManualInput har custom background, knappar, eyebrow, private controller, image focus och `Modularity/Module/ManualInput/Template`. Textmodulen finns i nya Modularity.                                | Återskapa smalare                                             | `406315a`, `852ec22`, `a3853b7`, `63283cb`, `81ab238`                                                        |
| Video och Iframe                                   | Ersätter/utökar video- och iframe-moduler, Mediaflow/embed-stöd och bevarar iframe-titlar i consent embeds.                                          | Temats Modularity har egna Video- och Iframe-moduler, men snabbpasset hittade ingen tydlig Mediaflow- eller `pre_getEmbedMarkup`-motsvarighet.                                                               | Återskapa smalare                                             | `9dba35b`, `93fb710`, `f7be0d8`, `db68141`, `b23b2a8`, `adcc631`, `c290c4e`                                  |
| FilesList, Contacts, Notice, Timeline och Sections | Lägger nya eller ersatta templates, icons, proseWrap, timeline/componentoverrides och section-/spacingfixar.                                         | Temats Modularity har motsvarande moduler och komponentbiblioteket har Timeline i styleguide.                                                                                                                | Verifiera manuellt per modul                                  | `28c0d7f`, `b03bdea`, `2e248f4`, `b43e33d`, `637db32`, `876af8a`, `e9ec9d1`                                  |
| MXUI-komponentlager                                | Egna komponentcontrollers/views för Card, Segment, Nav, Icon, Datebadge, Tags, Image, Modal och Timeline samt `MxBaseController` för filterkontrakt. | Komponentbibliotek `5.23.1` har modifier-filter, `TagSanitizerInterface`, id-sanitization, Material Symbols och nya komponentkontrakt.                                                                       | Återskapa smalare och verifiera visuellt                      | `05c1d2e`, `6106c13`, `8d1a5ec`, `13a8510`, `17613c7`, `64bcca4`, `b82e592`, `1f629b9`, `47b3cd9`            |
| Material Symbols och ikoner                        | Lokal Material Symbols-cache, SVG-hjälpare, MXUI icon component och fallbackfixar.                                                                   | Temat har `InlineMaterialSymbolsCssFeature`; komponentbiblioteket har `data-material-symbol` och bredare icon-stöd.                                                                                          | Ersätt med upstream där möjligt                               | `cc3e147`, `04d6f03`, `3ef1c5e`                                                                              |
| Uppladdade typsnitt                                | Egen uppladdning, MIME-stöd, Kirki-fontlistor och transientcache.                                                                                    | Temat har migrering mot WordPress native Font Library inklusive legacy uploaded fonts.                                                                                                                       | Ersätt                                                        | `7163c79`, `de75998`, `50b8ae4`                                                                              |
| Sök och ElasticPress                               | Egen `mx_search` AJAX-sök, direkt Elasticsearch-klient, boosting, decay, highlight, dokumentstöd, external pages, sökord och fel-loggning.           | Tema `7.7.18` har Algolia- och arkivsöklogik men ingen motsvarande ElasticPress AJAX-sök. Modularity har Algolia-indexattribut för moduler.                                                                  | Behåll, men hårdgranska säkerhet och dataformat               | `4adc9aa`, `2b72a79`, `ceb343b`, `5aecf10`, `3f31600`, `6b8598d`, `7c53f38`, `322e50e`, `7947c18`, `2bef2a7` |
| Async jobs                                         | CPT `async_job`, ACF-konfiguration, handlerregister, crontrigger, admin UI och AJAX-driven icke-blockerande körning.                                 | Ingen tydlig upstreammotsvarighet hittades. Tema har enbart specifika async attributes för arkiv och schemalagd theme-mod-lagring.                                                                           | Behåll om behovet finns, men säkerhetsgranska                 | `727e76b`, `bd89559`                                                                                         |
| Event Manager Integration                          | Stänger event hero overlay och ersätter EventForm-fält med API-/datalistbaserade organiser-/platsfält, sortering/filter och cache.                   | Integration `3.1.4` har redan `EventForm\Fields::get()` och samma `EventManagerIntegration/Module/EventForm/Fields`-filter. Extended överstyr ett befintligt upstreamfältträd.                               | Återskapa smalare i eventintegration eller verifiera manuellt | `1179092`, `8aeacd2`, `2d4c927`, `f46035d`, `0d0ee84`                                                        |
| Modularity Form Builder                            | Lägger default GDPR-notis via Customizer och frontendfixar för required file inputs/Safari.                                                          | Form Builder `4.0.12` äger backendflöden för filuppladdning, kryptering, submission och nedladdning.                                                                                                         | Behåll smal frontendfix om problemet finns kvar               | `bdcfa74`, `3f694a0`, `d588378`                                                                              |
| Theme/customizer-inställningar                     | Layout, header, print, typografi, knappar, page title, quote, MXUI-färger, fallbackbilder, module colors och hero search placeholder.                | Tema `7.7.18` har ny Customizer, styleguide och design-tokenstruktur. Flera gamla theme_mod-nycklar har bytt roll.                                                                                           | Mappa och verifiera manuellt                                  | `5bc67eb`, `90579fd`, `a244772`, `541a5a3`, `4c0443a`, `eb628bb`                                             |
| Theme mods import/export/clone                     | Tools-sida för export/import/kloning av theme mods mellan nätverkssajter, inklusive debug och loopback-HTTP-val.                                     | Temat har `LoadDesign` och `municipio_store_theme_mod`, men inte samma kloningsverktyg. Extendeds AJAX-export är publik via nopriv och bör inte bäras fram utan omdesign.                                    | Ersätt eller bygg om säkrare                                  | `5ed48d2`, `97cfe26`, `93a1093`, `48a3358`, `b1233dc`, `4d00878`                                             |
| 404 och error pages                                | Egen `page-not-found`-sida, queryflagg-reset och specialhantering för unmatched paths.                                                               | Temat `7.7.18` har `E404`-controller och Customizer-sektion för felsidor.                                                                                                                                    | Släpp eller migrera innehåll till tema                        | `fcf3150`, `3076ada`, `939d45c`, `adec484`                                                                   |
| Admin-/säkerhetspolicies                           | Stänger custom code, metadata TinyMCE-plugin, avancerad HTML, döljer metaboxar, tar bort användar-REST-endpoints och förenklar admin/editor.         | Temat har `CustomCodeInput`, men tidigare grep hittade inte LTS-filter/defaults för att stänga det. User REST-skydd och adminförenkling saknar tydlig upstreammotsvarighet.                                  | Behåll/återskapa smalt                                        | `2248a1f`, `4b88274`, `0ec482f`, `60cd8d4`, `be82bd4`                                                        |
| WordPress-/nätverksfixar                           | `wp_img_tag_add_auto_sizes`, attachment URL-cache, horizontal overflow-fix, e-postavsändare, robots upload blocking och diverse query-/rewritefixar. | Spridda eller saknade motsvarigheter i nya baspaket.                                                                                                                                                         | Verifiera manuellt och behåll selektivt                       | `218b2f2`, `85458ab`, övriga runtimefixar                                                                    |
| Migreringar och adminverktyg                       | Egna migrationsskript, aktivitetsloggkoppling, custom shortlinks till Redirection, init theme på subsites och 2FA.                                   | Ingen generell upstreammotsvarighet. Vissa är engångsmigreringar snarare än permanent pluginlogik.                                                                                                           | Behåll endast aktiva affärsregler                             | `4c72b21`, `9df1106`, `60cd8d4`, migrationscommits                                                           |
| Assets, språk och releasechurn                     | Byggda `dist/`, språkfiler, changelog, releaseversioner och formatting.                                                                              | Ska genereras av respektive paket efter implementation.                                                                                                                                                      | Ej relevant                                                   | `93dba43`, `711262b`, releasecommits                                                                         |

## Risker att verifiera

- `mx_search` går runt ElasticPress query integration med egen Elasticsearch-
  body. Kontrollera nonce, inputformat, indexmapping, dokumenthighlighting,
  multisite/EP_HOST och fel-loggning innan 2026-release.
- `async_job` har nopriv-AJAX för jobbkörning, cron-loop och cookies i intern
  loopbackrequest. Kontrollera capabilitymodell, race conditions och replayrisk.
- `theme-mods.php` exponerar theme-mod-export via
  `wp_ajax_nopriv_get_theme_mods` och klonar via loopback utan nonce på
  exportsteget. Bygg om innan eventuell återanvändning.
- `mod-navigation` använder aktuell post i frontend/headless REST-kontext.
  Verifiera `mx/module/current_post`, Nested Pages och GraphQL-fälten mot
  verkliga sidträd.
- `mod-posts` måste funktionstestas efter flytt till nya Posts-API:t eftersom
  äldre field keys och template controllers inte matchar temats nya arkitektur.
- MXUI-lagret kan krocka med komponentbibliotekets `TagSanitizer`,
  `sanitizeIdAttribute`, Material Symbols och modifierfilter.
- Eventformuläret överstyr samma fältfilter som upstream använder. LTS-fälten
  behöver schema-/submit-testas mot integration `3.1.4`.
- Theme_mod-nycklar för färger, layout, header och module appearance behöver
  mappas mot tema `7.7.18` innan import/export eller migrering.

## Kommandon körda

- `git rev-parse --short HEAD`
- `git branch -a`
- `git log --reverse --format=%h%x09%ad%x09%s --date=short ...`
- `git log --all --format=%h%x09%ad%x09%s --date=short -- ...`
- `rg` över Extended efter hooks, ACF-fält, AJAX, Modularity-, Municipio-,
  ComponentLibrary- och EventManagerIntegration-kontrakt.
- `git grep` i `wp-theme-municipio` `7.7.18` för Modularity, Posts, ManualInput,
  Search, theme mods, error pages, fonts, Material Symbols och
  säkerhetspolicies.
- `git grep`/`git show` i `wp-plugin-hbg-component-library` `5.23.1` för
  BaseController, modifierfilter, icon/material-symbols, sanitizer och field.
- `git grep`/`git show` i `wp-plugin-hbg-event-manager-integration` `3.1.4` för
  EventForm, Fields, SubmitEvent och cleanHero.
- `git grep` i `wp-plugin-modularity-form-builder` `4.0.12` för submission, file
  upload, encryption, nonce och formfält.
