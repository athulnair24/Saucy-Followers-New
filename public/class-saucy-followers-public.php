<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://mydigitalsauce.com/
 * @since      0.1.0
 *
 * @package    Saucy_Followers
 * @subpackage Saucy_Followers/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Saucy_Followers
 * @subpackage Saucy_Followers/public
 * @author     MyDigitalSauce <justin@mydigitalsauce.com>
 */
class Saucy_Followers_Public {

	private $plugin_name;
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles() {


		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {


		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'fdfp_vars', array(
			'processing_error' => __( 'There was a problem processing your request.', 'fdfp' ),
			'login_required'   => __( 'Oops, you must be logged-in to follow users.', 'fdfp' ),
			'logged_in'        => is_user_logged_in() ? 'true' : 'false',
			'ajaxurl'          => admin_url( 'admin-ajax.php' ),
			'nonce'            => wp_create_nonce( 'follow_nonce' )
		) );

	}

}
