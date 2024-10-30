<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

function mislider_delete_plugin() {
	global $wpdb;

	delete_option( 'moving-image-slider' );

	$wpdb->query( sprintf( "DROP TABLE IF EXISTS %s",
		$wpdb->prefix . 'moving_image_slider' ) );
}

mislider_delete_plugin();