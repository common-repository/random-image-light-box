<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

function rilb_delete_plugin() {
	global $wpdb;

	delete_option( 'random-image-light-box' );

	$wpdb->query( sprintf( "DROP TABLE IF EXISTS %s",
		$wpdb->prefix . 'randomimage_lb' ) );
}

rilb_delete_plugin();