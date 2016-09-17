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

	public function test_read_originals_from_file_non_existent_file() {
		$this->assertFalse( GP::$formats['json']->read_originals_from_file( __DIR__ . '/../data/foo.json' ) );
	}

	public function test_read_originals_from_file_invalid_file() {
		$this->assertFalse( GP::$formats['json']->read_originals_from_file( __DIR__ . '/../data/example-invalid.json' ) );
	}

	public function test_read_originals_from_file_missing_domain() {
		$this->assertFalse( GP::$formats['json']->read_originals_from_file( __DIR__ . '/../data/example-missing-domain.json' ) );
	}


	public function test_read_originals_from_file_missing_locale_data() {
		$this->assertFalse( GP::$formats['json']->read_originals_from_file( __DIR__ . '/../data/example-missing-locale-data.json' ) );
	}

	public function test_read_originals_from_file() {
		$expected = $this->data_example_originals();

		/* @var Translations $actual */
		$actual = GP::$formats['json']->read_originals_from_file( __DIR__ . '/../data/example-originals.json' );
		$this->assertSame( 5, count( $actual->entries ) );
		$this->assertEquals( $expected, $actual );
	}

	public function test_read_translations_from_file_non_existent_file() {
		$this->assertFalse( GP::$formats['json']->read_translations_from_file( __DIR__ . '/../data/foo.json' ) );
	}

	public function test_read_translations_from_file_invalid_file() {
		$this->assertFalse( GP::$formats['json']->read_translations_from_file( __DIR__ . '/../data/example-invalid.json' ) );
	}

	public function test_read_translations_from_file_missing_domain() {
		$this->assertFalse( GP::$formats['json']->read_translations_from_file( __DIR__ . '/../data/example-missing-domain.json' ) );
	}


	public function test_read_translations_from_file_missing_locale_data() {
		$this->assertFalse( GP::$formats['json']->read_translations_from_file( __DIR__ . '/../data/example-missing-locale-data.json' ) );
	}

	public function test_read_translations_from_file_missing_project() {
		$this->assertFalse( GP::$formats['json']->read_translations_from_file( __DIR__ . '/../data/example.json' ) );
	}

	public function test_read_translations_from_file() {
		GP::$original = $this->getMockBuilder( 'GP_Original' )->setMethods( array( 'by_project_id' ) )->getMock();
		GP::$original->expects( $this->once() )
		             ->method( 'by_project_id' )
		             ->with( $this->equalTo( 1 ) )
		             ->will( $this->returnValue( $this->data_get_stubbed_originals() ) );

		$expected = $this->data_example_translations();

		/* @var Translations $actual */
		$actual = GP::$formats['json']->read_translations_from_file( __DIR__ . '/../data/example.json', (object) array( 'id' => 1 ) );

		$this->assertSame( 5, count( $actual->entries ) );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Returns the expected data for the parsed example-untranslated.json file.
	 */
	public function data_example_originals() {
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

	public function data_get_stubbed_originals() {
		$stubbed_originals = array();

		/* @var Translation_Entry $translation_entry */
		foreach ( $this->data_example_originals()->entries as $translation_entry ) {
			$stubbed_originals[] = new GP_Original( array(
				'singular' => $translation_entry->singular,
				'context'  => $translation_entry->context,
			) );
		}

		return $stubbed_originals;
	}

	/**
	 * Returns the expected data for the parsed example-untranslated.json file.
	 */
	public function data_example_translations() {
		$translations = new Translations();
		$translations->add_entry( new Translation_Entry( array(
			'singular'     => 'This file is too big. Files must be less than %d KB in size.',
			'translations' => 'Diese Datei ist zu gross. Dateien müssen kleiner als %d KB sein.',
		) ) );
		$translations->add_entry( new Translation_Entry( array(
			'singular'     => '%d Theme Update',
			'translations' => '%d Theme-Aktualisierung',
			'plural'       => '%d Theme-Aktualisierungen',
		) ) );
		$translations->add_entry( new Translation_Entry( array(
			'singular'     => 'Medium',
			'context'      => 'password strength',
			'translations' => 'Medium',
		) ) );
		$translations->add_entry( new Translation_Entry( array(
			'singular'     => 'Category',
			'context'      => 'taxonomy singular name',
			'translations' => 'Kategorie',
		) ) );
		$translations->add_entry( new Translation_Entry( array(
			'singular'     => 'Pages',
			'context'      => 'post type general name',
			'translations' => 'Seiten',
		) ) );

		return $translations;
	}
}
