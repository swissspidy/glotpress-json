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

	public function test_read_originals_from_file_non_existent_file(  ) {
		$this->assertFalse( GP::$formats['json']->read_originals_from_file( __DIR__ .'/../data/foo.json' ) );
	}

	public function test_read_originals_from_file_invalid_file(  ) {
		$this->assertFalse( GP::$formats['json']->read_originals_from_file( __DIR__ .'/../data/example-invalid.json' ) );
	}

	public function test_read_originals_from_file(  ) {
		$expected = $this->data_example_untranslated();

		/* @var Translations $actual */
		$actual = GP::$formats['json']->read_originals_from_file( __DIR__ .'/../data/example-untranslated.json' );

		$this->assertSame( 5, count( $actual->entries ) );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Returns the expected data for the parsed example-untranslated.json file.
	 */
	public function data_example_untranslated(  ) {
		$translations = new Translations();
		$translations->add_entry( new Translation_Entry( array(
			'singular' => 'This file is too big. Files must be less than %d KB in size.',
		) ) );
		$translations->add_entry( new Translation_Entry( array(
			'singular' => '%d Theme Update',
		) ) );
		$translations->add_entry( new Translation_Entry( array(
			'singular' => 'Medium',
			'context'  => 'password strength',
		) ) );
		$translations->add_entry( new Translation_Entry( array(
			'singular' => 'Category',
			'context'  => 'taxonomy singular name',
		) ) );
		$translations->add_entry( new Translation_Entry( array(
			'singular' => 'Pages',
			'context'  => 'post type general name',
		) ) );

		return $translations;
	}
}
