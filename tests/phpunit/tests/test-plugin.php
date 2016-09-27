<?php

class Test_GlotPress_JSON extends GP_UnitTestCase {
	public function test_has_action() {
		$this->assertSame( 10, has_action( 'gp_init', 'gp_json_format_init' ) );
	}

	public function test_json_format_is_available() {
		$this->assertTrue( isset( GP::$formats['json'] ) );
	}

	public function test_jed1x_format_is_available() {
		$this->assertTrue( isset( GP::$formats['jed1x'] ) );
	}

	public function test_json_format_uses_plugin_class() {
		$this->assertTrue( GP::$formats['json'] instanceof GP_Format_JSON );
	}

	public function test_jed1x_format_uses_plugin_class() {
		$this->assertTrue( GP::$formats['jed1x'] instanceof GP_Format_JSON );
		$this->assertTrue( GP::$formats['jed1x'] instanceof GP_Format_Jed1x );
	}
}
