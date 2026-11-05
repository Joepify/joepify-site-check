<?php
// Test van de PHP-statuslogica zonder WordPress. Gebruik: php tests/status-test.php
define( 'ABSPATH', __DIR__ . '/' );
if ( ! function_exists( 'add_action' ) ) {
	function add_action() {}
}
require __DIR__ . '/../joepify-site-check.php';

$cases = array(
	array( '7.4.33', '2026-11-04', 'verlopen' ),
	array( '8.0.30', '2026-11-04', 'verlopen' ),
	array( '8.1.31', '2026-11-04', 'verlopen' ),
	array( '8.2.28', '2026-11-04', 'bijna' ),
	array( '8.2.28', '2027-01-02', 'verlopen' ),
	array( '8.3.14', '2026-11-04', 'ok' ),
	array( '8.4.1', '2026-11-04', 'ok' ),
	array( '9.0.0', '2026-11-04', 'onbekend' ),
);
$fail = 0;
foreach ( $cases as $c ) {
	$r  = joepify_site_check_php_status( $c[0], $c[1] );
	$ok = $r['status'] === $c[2];
	$fail += $ok ? 0 : 1;
	printf( "%s PHP %s op %s => %s (verwacht %s)\n", $ok ? 'OK  ' : 'FOUT', $c[0], $c[1], $r['status'], $c[2] );
}
$notices = array(
	array( '8.2.28', '2026-11-04', 'PHP 8.2 krijgt na 31 december 2026 geen beveiligingsupdates meer.' ),
	array( '8.1.31', '2026-11-04', 'PHP 8.1 krijgt sinds 31 december 2025 geen beveiligingsupdates meer.' ),
	array( '8.3.14', '2026-11-04', '' ),
	array( '7.4.33', '2026-11-04', 'PHP 7.4 krijgt geen beveiligingsupdates meer.' ),
);
foreach ( $notices as $n ) {
	$got = joepify_site_check_php_notice_text( $n[0], $n[1] );
	$ok  = $got === $n[2];
	$fail += $ok ? 0 : 1;
	printf( "%s melding PHP %s => \"%s\"\n", $ok ? 'OK  ' : 'FOUT', $n[0], $got );
}
exit( $fail ? 1 : 0 );
