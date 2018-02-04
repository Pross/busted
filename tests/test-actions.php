<?php
/**
 * Class BustedActions
 *
 * @package Busted
 */

class BustedActions extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		Storm_Busted::init();
	}

	public function test_init() {
		$this->assertEquals( 10, has_action( 'init', array( 'Storm_Busted', 'hooks' ) ) );
	}

	public function test_admin_init() {
		$this->assertEquals( 10, has_action( 'admin_init', array( 'Storm_Busted', 'hooks' ) ) );
	}

	public function test_wp_enqueue_scripts() {
		$this->assertEquals( Storm_Busted::get_priority(), has_action( 'wp_enqueue_scripts', array( 'Storm_Busted', 'wp_print_scripts' ) ) );
	}
}
