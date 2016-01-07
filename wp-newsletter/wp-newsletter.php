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
