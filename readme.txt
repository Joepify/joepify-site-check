=== Joepify Site Check ===
Contributors: joepify
Tags: security, php version, site health, dashboard, https
Requires at least: 5.8
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Dashboardwidget met PHP-versie en einddatum, updates, HTTPS, debugmodus, bestandsbewerking en XML-RPC. Geen externe verbindingen en geen tracking.

== Description ==

**Nederlands**

Joepify Site Check zet op je WordPress-dashboard een klein overzicht van de technische staat van je site. Alleen beheerders zien het.

Wat wordt getoond:

* **PHP-versie** met de datum waarop die tak geen beveiligingsupdates meer krijgt (PHP 8.1: 31 december 2025, PHP 8.2: 31 december 2026, PHP 8.3: 31 december 2027).
* **Beschikbare updates** voor WordPress, plugins en thema's.
* **HTTPS**: gebruikt de site een beveiligde verbinding?
* **Debugmodus**: staat `WP_DEBUG` aan, en worden fouten aan bezoekers getoond?
* **Bestanden bewerken** in het dashboard (`DISALLOW_FILE_EDIT`).
* **XML-RPC**: aan of uit.

Privacy en techniek:

* Geen externe verbindingen. De update-aantallen komen uit gegevens die WordPress zelf al heeft opgeslagen.
* Geen tracking, geen instellingen, geen database-tabellen. Bij verwijderen wordt de gebruikersinstelling opgeruimd.

**Melding bij PHP lager dan 8.3**

Draait de site op PHP 8.2 of ouder, dan toont de plugin voor beheerders op het dashboard en de pluginpagina één melding ("PHP 8.2 krijgt na 31 december 2026 geen beveiligingsupdates meer") met twee links: een link naar de gratis WordPress check van Joepify en een WhatsApp-bericht. Dit is een hulpaanbod van de plugin-auteur (een betaalde dienst). De melding is wegklikbaar per gebruiker en uit te zetten met `add_filter( 'joepify_site_check_show_notice', '__return_false' );`. De plugin zelf doet geen verzoeken; de links laden pas bij een klik.

Een controle van buitenaf, met ook plugins met bekende lekken en tekenen van een hack, doe je met de gratis [WordPress check](https://www.joepify.nl/wordpress-check). Hulp nodig bij onderhoud? Bekijk [WordPress specialist in Rotterdam](https://www.joepify.nl/wordpress-specialist-rotterdam).

**English**

Joepify Site Check adds a small dashboard widget for administrators showing your PHP version and its end-of-security-support date, pending core/plugin/theme updates, HTTPS, debug mode, file editing and XML-RPC. It makes no external requests and does no tracking. For PHP below 8.3 it shows one dismissible admin notice with links to paid help from the plugin author (disclosed here); disable it with the `joepify_site_check_show_notice` filter. PHP support dates come from php.net (checked 22 October 2026) and are built into the plugin, so update the plugin to refresh them.

== Installation ==

1. Upload the `joepify-site-check` folder to `/wp-content/plugins/` or install the zip via Plugins, Add New.
2. Activate the plugin.
3. Open the Dashboard. The widget "Site Check" appears for administrators.

== Frequently Asked Questions ==

= Verstuurt de plugin gegevens? =
Nee. Er zijn geen externe verbindingen.

= Waarom staan er geen instellingen? =
De plugin doet één ding en heeft geen configuratie nodig.

= Hoe actueel zijn de PHP-einddatums? =
Ze staan in de plugin (php.net, gecontroleerd 22 oktober 2026). Bij een nieuwe PHP-release komt er een plugin-update.

== Changelog ==

= 1.0.0 =
* Eerste versie, met een wegklikbare melding voor PHP lager dan 8.3.
