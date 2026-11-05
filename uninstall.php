<?php
// Verwijdert de gebruikersinstelling "melding verborgen" bij het verwijderen van de plugin.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
delete_metadata( 'user', 0, 'joepify_site_check_dismissed', '', true );
