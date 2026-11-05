<?php
/**
 * Plugin Name:       Joepify Site Check
 * Plugin URI:        https://github.com/Joepify/joepify-site-check
 * Description:       Dashboardwidget met de technische staat van je site: PHP-versie en einddatum, updates, HTTPS, debugmodus, bestandsbewerking en XML-RPC. Geen externe verbindingen (de knoppen zijn gewone links die pas laden als je erop klikt).
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Joepify
 * Author URI:        https://www.joepify.nl
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       joepify-site-check
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Einde van beveiligingsupdates per PHP-tak (bron: php.net/supported-versions, gecontroleerd 22 okt 2026).
 * Ouder dan 8.1 is altijd verlopen.
 */
function joepify_site_check_eol_dates() {
	return array(
		'8.1' => '2025-12-31',
		'8.2' => '2026-12-31',
		'8.3' => '2027-12-31',
		'8.4' => '2028-12-31',
		'8.5' => '2029-12-31',
	);
}

/**
 * Bepaal de status van een PHP-versie.
 *
 * @param string $version PHP-versie, bijvoorbeeld "8.2.30".
 * @param string $today   Datum als Y-m-d.
 * @return array{branch:string,eol:string,status:string,days:int|null}
 */
function joepify_site_check_php_status( $version, $today ) {
	$parts  = explode( '.', $version );
	$branch = $parts[0] . '.' . ( isset( $parts[1] ) ? $parts[1] : '0' );
	$dates  = joepify_site_check_eol_dates();

	if ( ! isset( $dates[ $branch ] ) ) {
		$older = version_compare( $branch, '8.1', '<' );
		return array(
			'branch' => $branch,
			'eol'    => $older ? '' : 'onbekend',
			'status' => $older ? 'verlopen' : 'onbekend',
			'days'   => null,
		);
	}

	$eol  = $dates[ $branch ];
	$days = (int) floor( ( strtotime( $eol ) - strtotime( $today ) ) / 86400 );

	if ( $days < 0 ) {
		$status = 'verlopen';
	} elseif ( $days <= 180 ) {
		$status = 'bijna';
	} else {
		$status = 'ok';
	}

	return array(
		'branch' => $branch,
		'eol'    => $eol,
		'status' => $status,
		'days'   => $days,
	);
}

/**
 * Tel beschikbare updates uit de bestaande (gecachte) WordPress-gegevens.
 * Er wordt hier niets extern opgehaald.
 */
function joepify_site_check_update_counts() {
	$core    = get_site_transient( 'update_core' );
	$plugins = get_site_transient( 'update_plugins' );
	$themes  = get_site_transient( 'update_themes' );

	$core_n = 0;
	if ( is_object( $core ) && ! empty( $core->updates ) && is_array( $core->updates ) ) {
		foreach ( $core->updates as $update ) {
			if ( isset( $update->response ) && 'upgrade' === $update->response ) {
				$core_n = 1;
				break;
			}
		}
	}

	return array(
		'core'    => $core_n,
		'plugins' => ( is_object( $plugins ) && ! empty( $plugins->response ) ) ? count( $plugins->response ) : 0,
		'themes'  => ( is_object( $themes ) && ! empty( $themes->response ) ) ? count( $themes->response ) : 0,
	);
}

/**
 * Verzamel alle controles als rijen: label, waarde, niveau (ok, let-op, fout).
 */
function joepify_site_check_rows() {
	$rows = array();

	$php = joepify_site_check_php_status( PHP_VERSION, gmdate( 'Y-m-d' ) );
	if ( 'verlopen' === $php['status'] ) {
		$php_text  = sprintf( 'PHP %s: geen beveiligingsupdates meer', PHP_VERSION );
		$php_level = 'fout';
	} elseif ( 'bijna' === $php['status'] ) {
		$php_text  = sprintf( 'PHP %s: beveiligingsupdates tot %s (nog %d dagen)', PHP_VERSION, $php['eol'], $php['days'] );
		$php_level = 'let-op';
	} elseif ( 'ok' === $php['status'] ) {
		$php_text  = sprintf( 'PHP %s: beveiligingsupdates tot %s', PHP_VERSION, $php['eol'] );
		$php_level = 'ok';
	} else {
		$php_text  = sprintf( 'PHP %s: einddatum onbekend', PHP_VERSION );
		$php_level = 'let-op';
	}
	$rows[] = array( 'PHP-versie', $php_text, $php_level );

	$u       = joepify_site_check_update_counts();
	$updates = sprintf( 'Core: %d, plugins: %d, thema\'s: %d', $u['core'], $u['plugins'], $u['themes'] );
	$rows[]  = array( 'Updates beschikbaar', $updates, ( $u['core'] + $u['plugins'] + $u['themes'] ) > 0 ? 'let-op' : 'ok' );

	$https  = is_ssl() && 0 === strpos( home_url(), 'https://' );
	$rows[] = array( 'HTTPS', $https ? 'Site gebruikt HTTPS' : 'Site gebruikt geen HTTPS', $https ? 'ok' : 'fout' );

	$debug  = defined( 'WP_DEBUG' ) && WP_DEBUG;
	$show   = $debug && ( ! defined( 'WP_DEBUG_DISPLAY' ) || WP_DEBUG_DISPLAY );
	$rows[] = array(
		'Debugmodus',
		$debug ? ( $show ? 'Aan, en fouten worden getoond aan bezoekers' : 'Aan (fouten worden niet getoond)' ) : 'Uit',
		$show ? 'fout' : ( $debug ? 'let-op' : 'ok' ),
	);

	$noedit = defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT;
	$rows[] = array( 'Bestanden bewerken in dashboard', $noedit ? 'Uitgezet (goed)' : 'Toegestaan', $noedit ? 'ok' : 'let-op' );

	$xmlrpc = (bool) apply_filters( 'xmlrpc_enabled', true );
	$rows[] = array( 'XML-RPC', $xmlrpc ? 'Aan' : 'Uit', $xmlrpc ? 'let-op' : 'ok' );

	return $rows;
}

/**
 * Nederlandse datum (bijvoorbeeld 31 december 2026) uit Y-m-d.
 */
function joepify_site_check_nl_date( $ymd ) {
	$maanden = array( '', 'januari', 'februari', 'maart', 'april', 'mei', 'juni', 'juli', 'augustus', 'september', 'oktober', 'november', 'december' );
	$p       = explode( '-', $ymd );
	return (int) $p[2] . ' ' . $maanden[ (int) $p[1] ] . ' ' . $p[0];
}

/**
 * Melding voor PHP lager dan 8.3. Geeft een lege string als er niets te melden is.
 * Voorbeeld: "PHP 8.2 krijgt na 31 december 2026 geen beveiligingsupdates meer."
 */
function joepify_site_check_php_notice_text( $version, $today ) {
	if ( version_compare( $version, '8.3', '>=' ) ) {
		return '';
	}
	$s = joepify_site_check_php_status( $version, $today );
	if ( '' === $s['eol'] || 'onbekend' === $s['eol'] ) {
		return sprintf( 'PHP %s krijgt geen beveiligingsupdates meer.', $s['branch'] );
	}
	if ( 'verlopen' === $s['status'] ) {
		return sprintf( 'PHP %s krijgt sinds %s geen beveiligingsupdates meer.', $s['branch'], joepify_site_check_nl_date( $s['eol'] ) );
	}
	return sprintf( 'PHP %s krijgt na %s geen beveiligingsupdates meer.', $s['branch'], joepify_site_check_nl_date( $s['eol'] ) );
}

function joepify_site_check_help_urls() {
	$branch = implode( '.', array_slice( explode( '.', PHP_VERSION ), 0, 2 ) );
	$text   = rawurlencode( 'Hoi Ayoub, mijn WordPress-site draait op PHP ' . $branch . '. Kun je meekijken?' );
	return array(
		'check'    => 'https://www.joepify.nl/wordpress-check?utm_source=wp-plugin',
		'whatsapp' => 'https://wa.me/31687195043?text=' . $text,
	);
}

/**
 * Is de melding voor deze gebruiker zichtbaar? Uit te zetten met het filter
 * add_filter( 'joepify_site_check_show_notice', '__return_false' );
 * of door de melding weg te klikken (per gebruiker, per PHP-tak).
 */
function joepify_site_check_notice_visible() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return false;
	}
	if ( ! apply_filters( 'joepify_site_check_show_notice', true ) ) {
		return false;
	}
	if ( '' === joepify_site_check_php_notice_text( PHP_VERSION, gmdate( 'Y-m-d' ) ) ) {
		return false;
	}
	$branch    = implode( '.', array_slice( explode( '.', PHP_VERSION ), 0, 2 ) );
	$dismissed = get_user_meta( get_current_user_id(), 'joepify_site_check_dismissed', true );
	return $dismissed !== $branch;
}

function joepify_site_check_help_buttons() {
	$u = joepify_site_check_help_urls();
	return sprintf(
		'<a class="button button-primary" href="%1$s" target="_blank" rel="noopener">%2$s</a> <a class="button" href="%3$s" target="_blank" rel="noopener">%4$s</a>',
		esc_url( $u['check'] ),
		esc_html__( 'Laat dit oplossen door Joepify', 'joepify-site-check' ),
		esc_url( $u['whatsapp'] ),
		esc_html__( 'Stuur een WhatsApp-bericht', 'joepify-site-check' )
	);
}

function joepify_site_check_admin_notice() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || ! in_array( $screen->id, array( 'dashboard', 'plugins' ), true ) ) {
		return;
	}
	if ( ! joepify_site_check_notice_visible() ) {
		return;
	}
	$dismiss = wp_nonce_url( add_query_arg( 'joepify_site_check_dismiss', '1' ), 'joepify_site_check_dismiss' );
	echo '<div class="notice notice-warning"><p><strong>Joepify Site Check:</strong> ';
	echo esc_html( joepify_site_check_php_notice_text( PHP_VERSION, gmdate( 'Y-m-d' ) ) );
	echo ' ' . esc_html__( 'Vraag je host om een nieuwere PHP-versie, of laat het door een specialist doen.', 'joepify-site-check' ) . '</p>';
	echo '<p>' . joepify_site_check_help_buttons() . ' '; // phpcs:ignore WordPress.Security.EscapeOutput -- onderdelen zijn al geëscaped.
	echo '<a href="' . esc_url( $dismiss ) . '">' . esc_html__( 'Verberg deze melding', 'joepify-site-check' ) . '</a></p></div>';
}
add_action( 'admin_notices', 'joepify_site_check_admin_notice' );

/**
 * Melding wegklikken: bewaart per gebruiker de PHP-tak waarvoor de melding is verborgen.
 */
function joepify_site_check_handle_dismiss() {
	if ( empty( $_GET['joepify_site_check_dismiss'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	check_admin_referer( 'joepify_site_check_dismiss' );
	$branch = implode( '.', array_slice( explode( '.', PHP_VERSION ), 0, 2 ) );
	update_user_meta( get_current_user_id(), 'joepify_site_check_dismissed', $branch );
	wp_safe_redirect( remove_query_arg( array( 'joepify_site_check_dismiss', '_wpnonce' ) ) );
	exit;
}
add_action( 'admin_init', 'joepify_site_check_handle_dismiss' );

function joepify_site_check_render_widget() {
	$labels = array(
		'ok'     => 'Goed',
		'let-op' => 'Let op',
		'fout'   => 'Actie nodig',
	);
	echo '<table class="widefat striped" style="border:0"><tbody>';
	foreach ( joepify_site_check_rows() as $row ) {
		printf(
			'<tr><td><strong>%1$s</strong></td><td>%2$s</td><td><em>%3$s</em></td></tr>',
			esc_html( $row[0] ),
			esc_html( $row[1] ),
			esc_html( $labels[ $row[2] ] )
		);
	}
	echo '</tbody></table>';
	if ( joepify_site_check_notice_visible() ) {
		echo '<p><strong>' . esc_html( joepify_site_check_php_notice_text( PHP_VERSION, gmdate( 'Y-m-d' ) ) ) . '</strong></p>';
		echo '<p>' . joepify_site_check_help_buttons() . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput -- onderdelen zijn al geëscaped.
	}
	echo '<p class="description">' . esc_html__( 'Alleen lokale gegevens. Er worden geen gegevens verstuurd.', 'joepify-site-check' ) . '</p>';
}

function joepify_site_check_register_widget() {
	if ( current_user_can( 'manage_options' ) ) {
		wp_add_dashboard_widget( 'joepify_site_check', __( 'Site Check', 'joepify-site-check' ), 'joepify_site_check_render_widget' );
	}
}
add_action( 'wp_dashboard_setup', 'joepify_site_check_register_widget' );
