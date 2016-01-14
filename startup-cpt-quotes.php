<?php
/*
Plugin Name: StartUp CPT Quotes
Description: Le plugin pour activer le Custom Post Quotes
Author: Yann Caplain
Version: 1.0.0
Text Domain: startup-cpt-quotes
Domain Path: /languages
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

//GitHub Plugin Updater
function startup_cpt_quotes_updater() {
	include_once 'lib/updater.php';
	//define( 'WP_GITHUB_FORCE_UPDATE', true );
	if ( is_admin() ) {
		$config = array(
			'slug' => plugin_basename( __FILE__ ),
			'proper_folder_name' => 'startup-cpt-quotes',
			'api_url' => 'https://api.github.com/repos/yozzi/startup-cpt-quotes',
			'raw_url' => 'https://raw.github.com/yozzi/startup-cpt-quotes/master',
			'github_url' => 'https://github.com/yozzi/startup-cpt-quotes',
			'zip_url' => 'https://github.com/yozzi/startup-cpt-quotes/archive/master.zip',
			'sslverify' => true,
			'requires' => '3.0',
			'tested' => '3.3',
			'readme' => 'README.md',
			'access_token' => '',
		);
		new WP_GitHub_Updater( $config );
	}
}

//add_action( 'init', 'startup_cpt_quotes_updater' );

//CPT
function startup_cpt_quotes() {
	$labels = array(
		'name'                => _x( 'Quotes', 'Post Type General Name', 'startup-cpt-quotes' ),
		'singular_name'       => _x( 'Quote', 'Post Type Singular Name', 'startup-cpt-quotes' ),
		'menu_name'           => __( 'Quotes', 'startup-cpt-quotes' ),
		'name_admin_bar'      => __( 'Quotes', 'startup-cpt-quotes' ),
		'parent_item_colon'   => __( 'Parent Item:', 'startup-cpt-quotes' ),
		'all_items'           => __( 'All Items', 'startup-cpt-quotes' ),
		'add_new_item'        => __( 'Add New Item', 'startup-cpt-quotes' ),
		'add_new'             => __( 'Add New', 'startup-cpt-quotes' ),
		'new_item'            => __( 'New Item', 'startup-cpt-quotes' ),
		'edit_item'           => __( 'Edit Item', 'startup-cpt-quotes' ),
		'update_item'         => __( 'Update Item', 'startup-cpt-quotes' ),
		'view_item'           => __( 'View Item', 'startup-cpt-quotes' ),
		'search_items'        => __( 'Search Item', 'startup-cpt-quotes' ),
		'not_found'           => __( 'Not found', 'startup-cpt-quotes' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'startup-cpt-quotes' )
	);
	$args = array(
		'label'               => __( 'quotes', 'startup-cpt-quotes' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'revisions' ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-format-quote',
		'show_in_admin_bar'   => false,
		'show_in_nav_menus'   => false,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
        'capability_type'     => array('quote','quotes'),
        'map_meta_cap'        => true
	);
	register_post_type( 'quotes', $args );
}

add_action( 'init', 'startup_cpt_quotes', 0 );

//Flusher les permalink à l'activation du plugin pour qu'ils fonctionnent sans mise à jour manuelle
function startup_cpt_quotes_rewrite_flush() {
    startup_cpt_quotes();
    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'startup_cpt_quotes_rewrite_flush' );

// Capabilities
function startup_cpt_quotes_caps() {	
	$role_admin = get_role( 'administrator' );
	$role_admin->add_cap( 'edit_quote' );
	$role_admin->add_cap( 'read_quote' );
	$role_admin->add_cap( 'delete_quote' );
	$role_admin->add_cap( 'edit_others_quotes' );
	$role_admin->add_cap( 'publish_quotes' );
	$role_admin->add_cap( 'edit_quotes' );
	$role_admin->add_cap( 'read_private_quotes' );
	$role_admin->add_cap( 'delete_quotes' );
	$role_admin->add_cap( 'delete_private_quotes' );
	$role_admin->add_cap( 'delete_published_quotes' );
	$role_admin->add_cap( 'delete_others_quotes' );
	$role_admin->add_cap( 'edit_private_quotes' );
	$role_admin->add_cap( 'edit_published_quotes' );
}

register_activation_hook( __FILE__, 'startup_cpt_quotes_caps' );

// Metaboxes
/**
 * Detection de CMB2. Identique dans tous les plugins.
 */
if ( !function_exists( 'cmb2_detection' ) ) {
    function cmb2_detection() {
        if ( !is_plugin_active('CMB2/init.php')  && !function_exists( 'startup_reloaded_setup' ) ) {
            add_action( 'admin_notices', 'cmb2_notice' );
        }
    }

    function cmb2_notice() {
        if ( current_user_can( 'activate_plugins' ) ) {
            echo '<div class="error message"><p>' . __( 'CMB2 plugin or StartUp Reloaded theme must be active to use custom metaboxes.', 'startup-cpt-quotes' ) . '</p></div>';
        }
    }

    add_action( 'init', 'cmb2_detection' );
}

function startup_cpt_quotes_meta() {
    
	// Start with an underscore to hide fields from custom fields list
	$prefix = '_startup_cpt_quotes_';

	$cmb_box = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => __( 'Quote details', 'startup-cpt-quotes' ),
		'object_types'  => array( 'quotes' )
	) );
    
    $cmb_box->add_field( array(
		'name'       => __( 'Author', 'startup-cpt-quotes' ),
		'id'         => $prefix . 'author',
		'type'       => 'text'
	) );

}

add_action( 'cmb2_admin_init', 'startup_cpt_quotes_meta' );
?>