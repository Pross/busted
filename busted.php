<?php
/*
Plugin Name: Busted!
Plugin URI: http://github.com/pdclark/busted
Description: Force browsers to load the most recent file if modified. Requires <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">wp_enqueue_style</a>, <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">wp_enqueue_script</a>, or <a href="http://codex.wordpress.org/Function_Reference/get_stylesheet_uri">get_stylesheet_uri</a> be used to load scripts and styles.
Version: 1.4
Author: Paul Clark
Author URI: http://pdclark.com
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

class Storm_Busted {

	/**
	 * @var string Name for query arguements and version identifier.
	 */
	static protected $version_slug = 'b-modified';

	static protected $priority = PHP_INT_MAX - 1;

	/**
	 * Setup hooks and vars.
	 *
	 * @return void
	 */
	static public function init() {

		add_action( 'init',       array( 'Storm_Busted', 'hooks' ) );
		add_action( 'admin_init', array( 'Storm_Busted', 'hooks' ) );
	}

	static public function get_priority() {
		return self::$priority;
	}

	static public function get_version_slug() {
		return self::$version_slug;
	}

	static public function hooks() {
		/**
		 * PHP_INT_MAX - 1 used as hook priority because many developers
		 * use wp_print_scripts for enqueues.
		 *
		 * Extremely high priority assures we catch everything.
		 */
		add_action( 'wp_enqueue_scripts',    array( 'Storm_Busted', 'wp_print_scripts' ), self::get_priority() );
		add_action( 'admin_enqueue_scripts', array( 'Storm_Busted', 'wp_print_scripts' ), self::get_priority() );
		add_filter( 'stylesheet_uri',        array( 'Storm_Busted', 'stylesheet_uri' ),   self::get_priority() );
		add_filter( 'locale_stylesheet_uri', array( 'Storm_Busted', 'stylesheet_uri' ),   self::get_priority() );
	}

	/**
	 * Update version in scripts and styles that use wp_enqueue_* functions.
	 *
	 * @return void
	 */
	static public function wp_print_scripts() {

		global $wp_scripts, $wp_styles;

		foreach ( array( $wp_scripts, $wp_styles ) as $enqueue_list ) {
			if ( ! isset( $enqueue_list->__busted_filtered ) && is_object( $enqueue_list ) ) {
				foreach ( (array) @ $enqueue_list->registered as $handle => $script ) {
					$modification_time = self::modification_time( $script->src );
					if ( $modification_time ) {
						$version = $script->ver . '-' . self::get_version_slug() . '-' . $modification_time;
						$enqueue_list->registered[ $handle ]->ver = $version;
					}
				}
				/**
				 * wp_print_scripts runs in header in footer and when called.
				 * Only run this modification once.
				 */
				$enqueue_list->__busted_filtered = true;
			}
		}
	}

	/**
	 * Filter styles and scripts that use stylesheet_uri()
	 *
	 * @param  string  $uri  URI
	 * @return string  URI
	 */
	static public function stylesheet_uri( $uri ) {

		if ( in_array( pathinfo( $uri, PATHINFO_EXTENSION ), array( 'css', 'js' ) ) ) {
			$uri = add_query_arg( self::get_version_slug(), self::modification_time( $uri ), $uri );
		}
		return esc_url( $uri );
	}

	/**
	 * @param  string $src Script relative path or URI
	 * @return int|bool File modification time or false.
	 */
	static public function modification_time( $src ) {

		if ( defined( 'BUSTEDTESTING' ) ) {
			return BUSTEDTESTING;
		}
		if ( false !== strpos( $src, content_url() ) ) {
			$src = WP_CONTENT_DIR . str_replace( content_url(), '', $src );
		}
		$file = realpath( $src );
		if ( file_exists( $file ) ) {
			return filemtime( $file );
		}
		return false;
	}
}
Storm_Busted::init();
