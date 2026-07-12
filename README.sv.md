# Municipio Extended

[English version](README.md)

Del av [Municipio LTS](https://github.com/municipio-se/municipio-lts). Lägger
till kompatibilitetsfixar, moduler, inställningar, adminverktyg och
integrationskod runt Municipio, Modularity, ACF, Kirki, ElasticPress och utvalda
WordPress-plugin.

## Krav

Municipio Extended kräver en Municipio LTS-installation med PHP `8.1+`. Aktivt
tema ska vara `municipio/wp-theme-municipio` i samma LTS-versionslinje som
resten av stacken.

Plugin som krävs är `municipio/wp-plugin-hbg-component-library`,
`municipio/wp-plugin-modularity`, `wpackagist-plugin/advanced-custom-fields` och
`wpackagist-plugin/kirki`.

## Funktioner

- **Anpassade posttyper och fält** – Driftmeddelanden, async-jobb,
  navigationsmodulfält, sidutseende, sidnavigation, arkivmeta, döljd rubrik,
  sökord och eventformulär.
- **Modularity-tillägg** – Egna templates, MXUI-kort/segment, modulgrupper,
  modulredigerare, modulwrappers, segment för manuellt innehåll, fillistor,
  iframe/video/kontakter/tidslinje och filtrering i inläggsmoduler.
- **Sök** – Egen AJAX-sökning, ElasticPress-frågor, resultatmappning,
  markeringar, posttyp-boostar, datumavtagning, content type-metadata,
  sökordsindexering och valbar felloggning since `v2025.12.11`.
- **Tema/customizer** – Rubrikbeteende, layoutbredder, typografi,
  headervarianter, utskriftsknapp, MXUI-färger, fallbackbilder, innehållslayout,
  arkivtabeller och modulgruppsbakgrunder.
- **Frontend** – Anpassad 404-rendering since `v2025.12.4`, gallerikolumner
  since `v2025.12.5`, horisontellt överflöd med `clip` since `v2025.12.9`,
  robots-blockering av uploads, RSS-prenumeration, widgets och knappnavigation i
  artiklar.
- **Media och assets** – Uppladdade typsnitt, Material Symbols-cache, lokala
  assets, editorstilar, Tailwind-hjälpare, ikonmodeller, bildmodeller och cache
  för attachment URL-uppslag.
- **Adminverktyg** – Export/import/kloning av theme mods, migrationsvy,
  förenklad redaktörsåtkomst, dolda adminobjekt, avstängt metadataplugin och
  valbara avancerade HTML-begränsningar.

## Kompatibilitet och fixar

- **WordPress** – Stänger REST-endpoints för användaruppräkning, stänger
  automatisk image `sizes`, normaliserar rewrite `with_front`, fixar attachment
  URL-cache och fixar argument för `attachment_updated` since `v2025.12.8`.
- **Municipio** – Lägger till view/controller/component-sökvägar, justerar
  headers, sidebar-klasser, breadcrumbs, content areas, anpassad 404,
  utskriftslänk, sökformulär och template view data.
- **Modularity** – Lägger till modulmallar, editor-UI, modulgrupper,
  sidebar-kompatibilitet och modulwrappers.
- **ACF/Kirki** – Lägger till lokala fält och customizerfält, läser in
  Kirki-stilar i editorn och skyddar inläsning av Material Symbols i editorn
  since `v2025.12.7`.
- **ElasticPress** – Hoppar över standardintegration för frågor, förbereder
  indexmetadata och exponerar filter för sökfrågor/resultat.
- **Event Manager Integration** – Stänger event hero-overlay, justerar taxonomy
  rewrite, eventformulärets val och eventmetadata.
- **Redirection** – Ger redaktörer åtkomst via konfigurerad roll, inklusive
  adminpanelens startsida since `v2025.12.6`.
- **Activity Log** – Loggar migrationsstatus när Activity Log är aktivt.
- **Two Factor** – Tvingar konfigurerade tvåfaktorsleverantörer.
- **Tracking GDPR** – Flyttar Municipio-specifik consent dialog-styling och
  hantering av script-attribut.

## Adminverktyg och migrationer

- **Migrationsfiler** – Lägg engångsmigrationer i `migrations/`.
- **Timeouts** – Använd `mx_migration_breakpoint()` i upprepningssäkra
  migrationer för att undvika PHP-timeouts.
- **Loggning** – Använd `mx_migration_progress_log()`,
  `mx_migration_error_log()`, `mx_migration_finish_log()` och
  `mx_migration_halt_log()` för migrationsloggar.
- **Status** – Se status i WP Admin under Verktyg -> Migrations.
- **Exempel** – Se `migrations/replace-mod-files1.php`.

## Sök och indexering

- **Endpoint** – `wp_ajax_mx_search` och `wp_ajax_nopriv_mx_search`.
- **Indexerad metadata** – `content_type`, `content_type_formatted`, ren text
  och `search_keywords`.
- **Rankning** – Fältmatchning, frasboostar, sökordsboostar, posttyp-boostar och
  valbar datumavtagning.
- **Resultatmappning** – Titel, utdrag, URL, bild, datum, typ och poäng.
- **Felloggning** – Styrs som standard av WordPress debug-inställningar och kan
  filtreras since `v2025.12.11`.
- **Jobbdatum** – Strängar utan tidszon tolkas i WordPress-tidszonen since
  `v2025.12.11`.

## Modularity och temabeteende

- **Navigationsmoduler** – Stödjer underliggande sidor, syskon, manuella objekt,
  meny som källa, ikoner, färger, beskrivningar och hide-if-empty.
- **Nested Pages** – Kan användas som källa för barn-/syskonnavigation med
  `mx_mod_navigation_use_nested_pages` since `v2025.12.1`.
- **Navigationsfixar** – Val av relaterat menyobjekt fixades since `v2025.12.2`;
  argumentordning för meny som källa fixades since `v2025.12.3`.
- **Modulgrupper** – Kan gruppera moduler per bakgrund och ignorera sidebars som
  inte stödjer bakgrunder.
- **Manuellt innehåll** – Stödjer segment och val av bildformat.
- **Färgförinställningar för textmoduler** – Avstängt som standard since
  `v2025.12.12`. Sätt `MUNICIPIO_EXTENDED_MOD_TEXT_USE_COLOR_PRESETS` till
  `true` eller returnera `true` från `mx_mod_text_use_color_presets` för att
  ersätta den äldre färgväljaren för textrutor med en förinställningslista
  baserad på Municipios palettfärger. Filtret körs efter konstantens
  standardvärde, så projektkod kan skriva över det i båda riktningar.
- **Inläggsmoduler** – Stödjer taxonomy-filtrering, sökmetadata och
  arkivtabellfält. På `decommission`-branchen ägs mixed-templaten i stället av
  det fristående pluginet Modularity Posts.
- **CSS för gallerikolumner** – Respekterar WordPress galleriklasser since
  `v2025.12.5`.

## Hook-referens

### Modeller och rendering

`apply_filters( 'mx/model/namespaces', string[] $namespaces, string $class, array $args )`
Ändra model namespaces som söks av `mx_get_model()`.

`apply_filters( 'mx/model/class', class-string|null $full_class, string $class )`
Ersätt den lösta modellklassen.

`apply_filters( 'mx/module_wrapper_attrs', array $attrs, array $args, string $postType, int $postId )`
Lägg till attribut på Modularity-modulwrappers.

`apply_filters( 'mx/module/current_post', WP_Post|null $post, MxModule $module )`
Ändra vilken post en modul renderas för. Standardvärdet är globala `$post`, som
saknas utanför loopen (t.ex. vid REST-anrop).

`apply_filters( 'mxui/debug_enabled', bool $enabled )` Aktivera
MXUI-debugutskrift.

### Komponentmodifierare

`apply_filters( 'ComponentLibrary/Component/Modifier', array $modifiers, mixed $context )`
Lägg till globala Component Library-modifierare.

`apply_filters( 'ComponentLibrary/Component/{Component}/Modifier', array $modifiers, mixed $context )`
Lägg till modifierare för en komponentklass.

`apply_filters( 'ComponentLibrary/Component/Icon/AltText', array $alt_text )`
Ersätt ikonernas alt-textkarta.

`apply_filters( 'ComponentLibrary/Component/Icon/AltTextPrefix', string $prefix )`
Ersätt prefix för ikonernas alt-texter.

`apply_filters( 'ComponentLibrary/Component/Icon/AltTextUndefined', string $alt_text )`
Ersätt fallbacktext för okända ikoner.

### Navigation

`apply_filters( 'mx_mod_navigation_fields', array $fields )` Ändra ACF-fält för
navigationsmodulen.

`apply_filters( 'mx_mod_navigation_use_nested_pages', bool $use_np, WP_Post $post, string $source, string $slug, int $id )`
Använd Nested Pages-data för barn-/syskonnavigation. since `v2025.12.1`

`apply_filters( 'mx/mod_navigation/hide_if_empty', bool $hide, string $slug, int $id, array $data )`
Styr om tomma navigationsmoduler ska döljas.

### Textmoduler

`apply_filters( 'mx_mod_text_use_color_presets', bool $enabled )`

Aktivera förinställningslistan för textmodulens textrutefärger. Standardvärdet
är `false`, eller värdet från `MUNICIPIO_EXTENDED_MOD_TEXT_USE_COLOR_PRESETS`
när konstanten är definierad. since `v2025.12.12`

`apply_filters( 'mx_mod_text_box_color_presets', array $presets )`

Ändra tillgängliga färgförinställningar för textmoduler, inklusive etiketter och
upplösta färgvärden. since `v2025.12.12`

### Sök

`apply_filters( 'mx_search_es_query', array $query, array $data, array $settings_post_types )`
Ändra grundläggande Elasticsearch bool-query.

`apply_filters( 'mx_search_boosted_post_types', array $boosted_post_types, array $data )`
Ändra posttyp-boostar.

`apply_filters( 'mx_search_boosted_post_type_functions', array $boosted_post_type_functions, array $data )`
Ändra genererade boost-funktioner.

`apply_filters( 'mx_search_decaying_post_types', array $decaying_post_types, array $data )`
Ändra posttyper med datumavtagning.

`apply_filters( 'mx_search_decaying_post_type_functions', array $decaying_post_type_functions, array $data )`
Ändra genererade datumavtagningsfunktioner.

`apply_filters( 'mx_search_es_function_score', array $function_score, array $data )`
Ändra Elasticsearch `function_score`-queryn.

`apply_filters( 'mx_search_es_body', array $es_body, array $data )` Ändra
slutlig Elasticsearch request body.

`apply_filters( 'mx_search_hit_source_mapping', array $hit_source_mapping, array $es_body, array $data )`
Ändra callbacks för hit-till-resultat-mappning.

`apply_filters( 'mx_search_es_hit', array $transformed_hit, array $hit, array $es_results, array $es_body, array $data )`
Ändra en transformerad sökträff.

`apply_filters( 'mx_search_results', array $results, array $es_results )` Ändra
slutligt AJAX-söksvar.

`apply_filters( 'mx_search_error_logging_enabled', bool $enabled )` Slå på eller
av felloggning för sök. since `v2025.12.11`

`apply_filters( 'mx_search_error_log_path', string $log_path )` Ändra sökväg
till felloggfil. since `v2025.12.11`

`apply_filters( 'mx_search_post_content_type', string $content_type, array $post_args, int $post_id )`
Ändra indexerad content type.

`apply_filters( 'mx_search_post_content_type_formatted', string $label, array $post_args, int $post_id )`
Ändra indexerad content type-etikett.

### Arkiv, inlägg och media

`apply_filters( 'mx/meta_field/label', string $label, string $meta_field )`
Ändra etiketter för arkivmetafält.

`apply_filters( 'mx/meta_field/display_value', mixed $value, string $field )`
Ändra visningsvärden för arkivmetafält.

`apply_filters( 'mx_post_types_with_front', string[] $post_types )` Behåll
`with_front` för valda posttyper.

`apply_filters( 'mx_taxonomies_with_front', string[] $taxonomies )` Behåll
`with_front` för valda taxonomier.

`apply_filters( 'mx_materialsymbols_cache_path', string $path )` Ändra
cachekatalog för Material Symbols.

`apply_filters( 'mx_materialsymbols_cache_url', string $url )` Ändra cache-URL
för Material Symbols.

`apply_filters( 'mx_should_ignore_module_group_backgrounds', bool $ignore_backgrounds, string $sidebar, mixed $context, array $visible_sidebars )`
Styr stöd för modulgruppsbakgrunder per sidebar/context.

## Utveckling och Bidrag

Läs mer om hur du kan bidra i [CONTRIBUTING.md-filen](CONTRIBUTING.md).
