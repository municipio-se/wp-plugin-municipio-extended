# Ändringslogg

[English version](CHANGELOG.md)

## 2025.12.13 – 2026-06-16

- **Taxonomier för mixed posts** – Gjorde den befintliga växeln för
  taxonomivisning tillgänglig för inläggsmoduler med visningsläget mixed, så att
  valda taxonomitermer kan läggas till i mixed post-data.
  [PR #12](https://github.com/municipio-se/wp-plugin-municipio-extended/pull/12).
- **Navigationskontext** – Lade till filtret `mx/module/current_post` och
  använde det när children- och sibling-navigation hämtas, så att rendering
  utanför loopen kan ange aktuell sida när globala `$post` saknas.
  [PR #13](https://github.com/municipio-se/wp-plugin-municipio-extended/pull/13).

## 2025.12.12 – 2026-06-01

- **Textmodulfärger** – Lade till valbara färgförinställningar som hämtas från
  den aktiva Municipio-färgpaletten och kan justeras med projektspecifika
  filter.
  [PR #11](https://github.com/municipio-se/wp-plugin-municipio-extended/pull/11).
- **Iframe-tillgänglighet** – Bevarade iframe-titlar när Modularitys
  iframe-moduler och råa innehålls-iframes renderas via samtyckesmedvetna
  WSTG-inbäddningar.

## 2025.12.11 – 2026-05-15

- Fixade tolkning av jobbdatum utan tidszon så att datumsträngar tolkas i
  WordPress-webbplatsens tidszon. Det förhindrar att ansökningstider från Visma
  Recruit flyttas till nästa kalenderdag på webbplatser som använder exempelvis
  Europe/Stockholm.
  [PR #9](https://github.com/municipio-se/wp-plugin-municipio-extended/pull/9).
- Lade till konfigurerbar felloggning för Elasticsearch-sökning. Loggningen
  respekterar `WP_DEBUG` och `WP_DEBUG_LOG` som standard och kan styras med
  filtren `mx_search_error_logging_enabled` och `mx_search_error_log_path`.
  [PR #8](https://github.com/municipio-se/wp-plugin-municipio-extended/pull/8).
- Tack @michaelclaesson för ditt bidrag!

## 2025.12.10 – 2026-05-08

- Byggde om distribuerade CSS-assets så att fixen för horisontellt överflöd från
  `2025.12.9` finns med i paketerade assets.

## 2025.12.9 – 2026-05-08

- Ändrade global hantering av horisontellt överflöd från `overflow-x: hidden`
  till `overflow-x: clip` för att mer tillförlitligt förhindra oönskad
  horisontell scrollning.

## 2025.12.8 – 2026-05-05

- Fixade callback-signaturen för hooken `attachment_updated` så att den matchar
  de tre argument som WordPress skickar med, vilket förhindrar
  `ArgumentCountError` vid uppdateringar av migrerade eller importerade bilagor
  och behåller transient-cache-invalideringen.

## 2025.12.7 – 2026-03-03

- Fixade ett fatalt fel när Material Symbols-typsnittet från Municipio-temat
  köades in i editorn innan Municipio-temats konstanter är tillgängliga.

## 2025.12.6 – 2026-03-02

- Gav redaktörer åtkomst till WordPress adminpanels startsida efter inloggning.
  [PR #123](https://github.com/municipio-lts/wp-plugin-municipio-extended-2024/pull/123).

## 2025.12.5 – 2026-02-17

- Fixade styling för WordPress-gallerier så att inställt antal gallerikolumner
  respekteras.

## 2025.12.4 – 2026-02-02

- Fixade hantering av anpassad 404-sida för omatchade sökvägar genom att bara
  tvinga 404-status när ingen rewrite-regel, inget inläggsarkiv och ingen giltig
  sida matchar, och därefter rendera den valda 404-sidan som en vanlig sida.

## 2025.12.3 – 2026-01-15

- Fixade navigationsmoduler med meny som källa genom att skicka argumenten för
  menydjup och förälder i rätt ordning.

## 2025.12.2 – 2026-01-14

- Fixade Nested Pages-baserad navigation så att associerade menyobjekt
  kontrolleras tills rätt relaterat objekt med underobjekt hittas.

## 2025.12.1 – 2026-01-12

- Lade till filtret `mx_mod_navigation_use_nested_pages` så att
  navigationsmoduler för underliggande och närliggande sidor kan använda Nested
  Pages-menyn istället för sidträdet.
- Exponerade återanvändbar inläsning av menyobjekt via
  `ModNavigation::getMenuItemsByMenu()`.

## 2025.12.0 – 2025-12-30

- Lade till en `LICENSE`-fil i rotkatalogen.
- Uppdaterade Composer-paketets licensmetadata från `AGPL-3.0` till
  `GPL-2.0-or-later`.
- Uppdaterade README-beskrivningen så att pluginet positioneras som en del av
  Municipio LTS.
