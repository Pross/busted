<?php
/**
 * Class BustedVars
 *
 * @package Busted
 */

class BustedVars extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		Storm_Busted::init();
	}

	public function test_priority() {
		$this->assertEquals( PHP_INT_MAX - 1, Storm_Busted::get_priority() );
	}

	public function test_version_slug() {
		$this->assertEquals( 'b-modified', Storm_Busted::get_version_slug() );
	}
}
