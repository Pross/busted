<?php
/**
 * Class BustedFilters
 *
 * @package Busted
 */

class BustedFilters extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		Storm_Busted::init();
	}

	public function test_stylesheet_uri() {
		$this->assertEquals( 10, has_filter( 'stylesheet_uri', array( 'Storm_Busted', 'stylesheet_uri' ) ) );
	}

	public function test_locale_stylesheet_uri() {
		$this->assertEquals( 10, has_filter( 'locale_stylesheet_uri', array( 'Storm_Busted', 'stylesheet_uri' ) ) );
	}
}
