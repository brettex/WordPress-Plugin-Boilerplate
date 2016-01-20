<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://primitivespark.com
 * @since             1.0.0
 * @package           wp_newsletter
 *
 * @wordpress-plugin
 * Plugin Name:       WP Newsletter
 * Plugin URI:        http://primitivespark.com/wp-newsletter/
 * Description:       This plugin allows you to create a responsive HTML Email template that via Custom Post Type that can be scraped by iContact or GoDaddy for use in email campaigns
 * Version:           1.0.0
 * Author:            Primitive Spark
 * Author URI:        http://primitivespark.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-newsletter
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/** Add new Post Type for the WP Newsletter **/
add_action( 'init', 'create_post_type' );

function create_post_type() {
	$labels = array(
		'name'               => _x( 'Newsletter', 'post type general name', 'wp-newsletter' ),
		'singular_name'      => _x( 'Newsletter', 'post type singular name', 'wp-newsletter' ),
		'menu_name'          => _x( 'Newsletters', 'admin menu', 'wp-newsletter' ),
		'name_admin_bar'     => _x( 'Newsletter', 'add new on admin bar', 'wp-newsletter' ),
		'add_new'            => _x( 'Add New', 'newsletter', 'wp-newsletter' ),
		'add_new_item'       => __( 'Add New Newsletter', 'wp-newsletter' ),
		'new_item'           => __( 'New Newsletter', 'wp-newsletter' ),
		'edit_item'          => __( 'Edit Newsletter', 'wp-newsletter' ),
		'view_item'          => __( 'View Newsletter', 'wp-newsletter' ),
		'all_items'          => __( 'All Newsletters', 'wp-newsletter' ),
		'search_items'       => __( 'Search Newsletters', 'wp-newsletter' ),
		'parent_item_colon'  => __( 'Parent Newsletters:', 'wp-newsletter' ),
		'not_found'          => __( 'No newsletters found.', 'wp-newsletter' ),
		'not_found_in_trash' => __( 'No newsletters found in Trash.', 'wp-newsletter' )
	);

	$args = array(
		'labels'             => $labels,
        'description'        => __( 'Description.', 'wp-newsletter' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'newsletter', 'with_front' => false ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor','custom-fields','thumbnail'  )
	);

	register_post_type( 'wpn_newsletter', $args );
}


/** Add filter to load a custom template **/
add_filter( 'template_include', 'wpn_template_chooser');
 
 
/**
 * Returns template file
 *
 * @since 1.0
 */
 
function wpn_template_chooser( $template ) {
 
    // Post ID
    $post_id = get_the_ID();
 
    // For all other CPT
    if ( get_post_type( $post_id ) != 'wpn_newsletter' ) {
        return $template;
    }
 
    // Else use custom template
    if ( is_single() ) {
        return wpn_get_template_hierarchy( 'wpn_single' );
    }
 
}

function wpn_get_template_hierarchy( $template ) {
 
    // Get the template slug
    $template_slug = rtrim( $template, '.php' );
    $template = $template_slug . '.php';
 
    // Check if a custom template exists in the theme folder, if not, load the plugin template file
    if ( $theme_file = locate_template( array( 'wpn_template/' . $template ) ) ) {
        $file = $theme_file;
    }
    else {
        $file = plugin_dir_path( __FILE__ ) . '/templates/' . $template;
    }
 
    return apply_filters( $template, $file );
}
 

/***** 
    ADD CUSTOM FIELDS TO THE NEWSLETTER CONTENT TYPE
     
*****/
/** Add Meta Boxes **/

/**
 * Calls the class on the post edit screen.
 */
function call_addMetaClass() {
	new metaClass();
}

if ( is_admin() ) {
	add_action( 'load-post.php', 'call_addMetaClass' );
	add_action( 'load-post-new.php', 'call_addMetaClass' );
}

/** 
 * The Class.
 */
class metaClass {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box( $post_type ) {
		$post_types = array('wpn_newsletter');   //limit meta box to certain post types
		if ( in_array( $post_type, $post_types )) {
			add_meta_box(
				'wp_newsletter_box_name'
				,__( 'Sub Title', 'wp_newsletter' )
				,array( $this, 'render_meta_box_content' )
				,$post_type
				,'advanced'
				,'high'
			);
		}
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {
	
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['wp_newsletter_inner_custom_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['wp_newsletter_inner_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'wp_newsletter_inner_custom_box' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
		// so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
	
		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		/* OK, its safe for us to save the data now. */

		// Sanitize the user input.
		$mydata = sanitize_text_field( $_POST['wp_newsletter_sub_title'] );

		// Update the meta field.
		update_post_meta( $post_id, '_wp_newsletter_sub_title', $mydata );
	}


	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box_content( $post ) {
	
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'wp_newsletter_inner_custom_box', 'wp_newsletter_inner_custom_box_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$value = get_post_meta( $post->ID, '_wp_newsletter_sub_title', true );

		// Display the form, using the current value.
		echo '<label for="wp_newsletter_sub_title">';
		_e( 'Enter a sub title for the Newsletter', 'wp_newsletter' );
		echo '</label> ';
		echo '<input type="text" id="wp_newsletter_sub_title" name="wp_newsletter_sub_title"';
		echo ' value="' . esc_attr( $value ) . '" size="50" />';
	}
}

/** Add Newsletter Menu option to Admin Menu **/
add_action('admin_menu', 'WPNewsletter_admin_menu');

function WPNewsletter_admin_menu() {
	add_options_page(__('WP Newsletter', 'wp-newsletter'), __('WP Newsletter', 'wp-newsletter'), 8, 'newsletter', 'newsletter_home');
}

function newsletter_home(){
    include_once plugin_dir_path( __FILE__ ) . 'admin/partials/wp-newsletter-admin-display.php';
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-newsletter-activator.php
 */
function activate_wp_newsletter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-newsletter-activator.php';
	wp_newsletter_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-newsletter-deactivator.php
 */
function deactivate_wp_newsletter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-newsletter-deactivator.php';
	wp_newsletter_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_newsletter' );
register_deactivation_hook( __FILE__, 'deactivate_wp_newsletter' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-newsletter.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_newsletter() {

	$plugin = new wp_newsletter();
	$plugin->run();

}
run_wp_newsletter();
