<?php
/**
 * Uninstall Cooked
 *
 * @package Cooked
 * @since 1.14.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete all plugin options.
$options = [
	'cooked_settings',
	'cooked_settings_saved',
	'cooked_version',
	'cooked_settings_version',
	'cooked_pro_settings_version',
	'cooked_delicious_recipes_imported',
	'cooked_wp_recipe_maker_imported',
	'cooked_related_version',
	'cooked_related_calculation_last',
];

foreach ( $options as $option ) {
	delete_option( $option );
}

// Delete transients.
delete_transient( 'cooked_classic_recipes' );
delete_transient( 'cooked_widget_recipes_list' );

// Clean up legacy related-recipes transients.
global $wpdb;
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_cooked_%' OR option_name LIKE '_transient_timeout_cooked_%'" );

// Remove custom role and capabilities.
if ( class_exists( 'WP_Roles' ) ) {
	$wp_roles = new WP_Roles();

	$roles = [ 'subscriber', 'contributor', 'author', 'editor', 'cooked_recipe_editor', 'administrator' ];
	$caps  = [
		'edit_cooked_recipes',
		'edit_cooked_settings',
		'approve_cooked_recipes',
		'delete_cooked_recipes',
		'edit_cooked_default_template',
	];

	foreach ( $roles as $role ) {
		foreach ( $caps as $cap ) {
			$wp_roles->remove_cap( $role, $cap );
		}
	}

	remove_role( 'cooked_recipe_editor' );
}

flush_rewrite_rules();
