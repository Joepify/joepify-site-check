# Joepify Site Check (WordPress-plugin)

Een kleine WordPress-plugin die op het dashboard toont of je site technisch op orde is: **PHP-versie met einddatum, beschikbare updates, HTTPS, debugmodus, bestandsbewerking en XML-RPC**. Geen instellingen, geen externe verbindingen en geen tracking.

## Voor wie

Site-eigenaren en beheerders die in één oogopslag willen zien waar een WordPress-site achterloopt, zonder weer een zwaar beveiligingspakket te installeren.

## Wat je ziet

| Controle | Wat er gebeurt |
|---|---|
| PHP-versie | Toont de versie en tot wanneer die beveiligingsupdates krijgt. PHP 8.1 verliep op 31 december 2025, PHP 8.2 verloopt op 31 december 2026. Binnen 180 dagen staat de regel op "Let op", daarna op "Actie nodig" |
| Updates | Aantal beschikbare updates voor core, plugins en thema's |
| HTTPS | Draait de site over HTTPS |
| Debugmodus | `WP_DEBUG` aan, en of fouten aan bezoekers worden getoond |
| Bestanden bewerken | Staat `DISALLOW_FILE_EDIT` aan |
| XML-RPC | Aan of uit |

Alleen gebruikers met de rechten van een beheerder (`manage_options`) zien de widget.

## De melding bij PHP lager dan 8.3

Draait de site op PHP 8.2 of ouder, dan toont de plugin op het dashboard en de pluginpagina **een** melding, bijvoorbeeld: "PHP 8.2 krijgt na 31 december 2026 geen beveiligingsupdates meer." Daaronder staan twee knoppen: "Laat dit oplossen door Joepify" (naar de [WordPress check](https://www.joepify.nl/wordpress-check?utm_source=github&utm_medium=readme&utm_campaign=joepify-site-check)) en een WhatsApp-bericht. Dat is een hulpaanbod van de makers van de plugin; zeg dat gerust tegen je klanten als je de plugin voor hen installeert.

- **Wegklikbaar:** "Verberg deze melding" onthoudt per gebruiker voor welke PHP-tak je hem hebt verborgen. Gaat de site naar een andere tak, dan komt de melding één keer terug.
- **Uit te zetten:** `add_filter( 'joepify_site_check_show_notice', '__return_false' );`
- **Alleen beheerders** zien hem, alleen op het dashboard en de pluginpagina.
- **De knoppen zijn gewone links.** De plugin doet zelf geen verzoek; er wordt niets verstuurd tot je klikt. De plugin voert niets automatisch uit en past niets aan.
- Bij verwijderen van de plugin wordt de gebruikersinstelling opgeruimd (`uninstall.php`).

## Installeren

1. Download of clone deze repo.
2. Zet de map `joepify-site-check` (met `joepify-site-check.php`) in `wp-content/plugins/`.
3. Activeer de plugin en open het dashboard.

## Privacy en techniek

- **Geen externe verbindingen.** De update-aantallen komen uit de gegevens die WordPress al heeft opgeslagen (`update_core`, `update_plugins`, `update_themes`).
- **Geen tracking, geen database-tabellen, geen opties.**
- Alle uitvoer wordt geëscaped (`esc_html`).
- De PHP-einddatums staan in de code (bron: php.net, gecontroleerd 22 oktober 2026) en zijn te vinden in `joepify_site_check_eol_dates()`.

## Testen

Het PHP-bestand is gecontroleerd met `php -l`. De logica voor de PHP-status is een losse functie zonder WordPress-afhankelijkheid en getest met PHP 8.4 (zie `tests/status-test.php`):

```bash
php -l joepify-site-check.php
php tests/status-test.php
```

## Meer dan het dashboard

Deze widget kijkt alleen naar binnen. Wil je ook van buitenaf zien of een site verouderde plugins heeft of tekenen van een hack, gebruik dan de gratis [WordPress check](https://www.joepify.nl/wordpress-check?utm_source=github&utm_medium=readme&utm_campaign=joepify-site-check). Voor hulp bij het bijhouden van een WordPress-site: [WordPress specialist in Rotterdam](https://www.joepify.nl/wordpress-specialist-rotterdam?utm_source=github&utm_medium=readme&utm_campaign=joepify-site-check). Op de command line werkt [php-eol-check](https://github.com/Joepify/php-eol-check) voor elke site.

## Laat het mij doen

Ik werk de PHP-versie van je WordPress-site veilig bij en test dat alles blijft werken. Vaste prijs: vanaf €79 eenmalig (excl. btw). [Controleer eerst je site](https://www.joepify.nl/wordpress-check?utm_source=github&utm_medium=readme&utm_campaign=joepify-site-check) of [stuur een WhatsApp-bericht](https://wa.me/31687195043?text=Hoi%20Ayoub%2C%20ik%20kom%20via%20GitHub%20%28joepify-site-check%29%20en%20wil%20hulp%20met%20php%20update.).

## Over Joepify

[Joepify](https://www.joepify.nl?utm_source=github&utm_medium=readme&utm_campaign=joepify-site-check) is een webbureau aan de Metaalhof 27 in Rotterdam. Ayoub Assaidi bouwt en onderhoudt WordPress-sites zelf.

## English

A single-file WordPress plugin that adds an admin-only dashboard widget with your PHP version and its security end-of-life date, pending updates, HTTPS, debug mode, file editing and XML-RPC status. No external requests, tracking or settings. For PHP below 8.3 it shows one dismissible notice with an optional link to paid help from the plugin authors; it can be disabled with a filter. `readme.txt` follows the wordpress.org format (Dutch and English).

## Licentie

GPL-2.0-or-later, zie [LICENSE](LICENSE).
