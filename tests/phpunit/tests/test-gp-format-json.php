<?php

class Test_GP_Format_JSON extends GP_UnitTestCase {
	public function test_json_format_is_available() {
		$this->assertTrue( isset( GP::$formats['json'] ) );
	}

	public function test_json_format_uses_plugin_class() {
		$this->assertTrue( GP::$formats['json'] instanceof GP_Format_JSON );
	}
}
