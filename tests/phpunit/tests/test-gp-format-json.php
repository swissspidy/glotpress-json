<?php

class Test_GP_Format_JSON extends GP_UnitTestCase {
	/**
	 * @var GP_Translation_Set
	 */
	public $translation_set;

	/**
	 * @var GP_Locale
	 */
	public $locale;

	public function setUp() {
		parent::setUp();

		$this->translation_set = $this->factory->translation_set->create_with_project_and_locale( array(), array( 'name' => 'foo_project' ) );

		$this->locale = new GP_Locale( array(
			'slug' => $this->translation_set->locale,
		) );
	}

	public function test_json_format_is_available() {
		$this->assertTrue( isset( GP::$formats['json'] ) );
	}

	public function test_json_format_uses_plugin_class() {
		$this->assertTrue( GP::$formats['json'] instanceof GP_Format_JSON );
	}

	public function test_format_name() {
		$this->assertSame( 'JSON (.json)', GP::$formats['json']->name );
	}

	public function test_format_extension() {
		$this->assertSame( 'json', GP::$formats['json']->extension );
	}

	public function test_print_exported_file_can_be_decoded() {
		$entries = array(
			new Translation_Entry( array( 'singular' => 'foo', 'translations' => array( 'foox' ) ) ),
		);

		$json = GP::$formats['json']->print_exported_file( $this->translation_set->project, $this->locale, $this->translation_set, $entries );

		$this->assertNotNull( json_decode( $json, true ) );
	}
}
