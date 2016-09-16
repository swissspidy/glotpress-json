<?php
/**
 * GP Format JSON class
 *
 * @since 0.1.0
 */

/**
 * Format class used to support SRT file format.
 *
 * @since 0.1.0
 */
class GP_Format_JSON extends GP_Format {
	/**
	 * Name of file format, used in file format dropdowns.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $name = 'JSON (.json)';

	/**
	 * File extension of the file format, used to autodetect formats and when creating the output file names.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $extension = 'json';

	/**
	 * Generates a string the contains the $entries to export in the JSON file format.
	 *
	 * @since 0.1.0
	 *
	 * @param GP_Project         $project         The project the strings are being exported for, not used
	 *                                            in this format but part of the scaffold of the parent object.
	 * @param GP_Locale          $locale          The locale object the strings are being exported for. not used
	 *                                            in this format but part of the scaffold of the parent object.
	 * @param GP_Translation_Set $translation_set The locale object the strings are being
	 *                                            exported for. not used in this format but part
	 *                                            of the scaffold of the parent object.
	 * @param GP_Translation     $entries         The entries to export.
	 * @return string
	 */
	public function print_exported_file( $project, $locale, $translation_set, $entries ) {
		$result = array(
			'domain'      => 'messages',
			'locale_data' => array(
				'messages' => array(
					'__GP_EMPTY__' => array(
						'domain'       => 'messages',
						'plural-forms' => $locale->plural_expression,
						'lang'         => $locale->slug,
					),
				),
			),
		);

		/* @var Translation_Entry $entry */
		foreach ( $entries as $entry ) {
			$key = $entry->context ? $entry->context . chr( 4 ) . $entry->singular : $entry->singular;

			$result['locale_data']['messages'][ $key ] = array_filter( $entry->translations, function ( $translation ) {
				return null !== $translation;
			} );
		}

		$result = wp_json_encode( $result );

		/*
		 * Replace '__GP_EMPTY__' with an acttual empty string.
		 *
		 * Empty object property names are not supported in PHP, so they would get lost.
		 *
		 * Note: When decoding, PHP replaces empty strings with '_empty_'.
		 *
		 * @see https://bugs.php.net/bug.php?id=50867
		 */

		return str_replace( '__GP_EMPTY__', '', $result );
	}

	/**
	 * Reads a set of original strings from a JSON file.
	 *
	 * @since 0.1.0
	 *
	 * @param string $file_name The name of the uploaded JSON file.
	 * @return Translations|bool
	 */
	public function read_originals_from_file( $file_name ) {
		$file = file_get_contents( $file_name );

		if ( ! $file ) {
			return false;
		}

		$json = $this->json_decode( $file );

		if ( ! $json ) {
			return false;
		}

		$entries = new Translations();

		foreach ( $json['locale_data'][ $json['domain'] ] as $key => $value ) {
			if ( '' === $key ) {
				continue;
			}

			$args = array(
				'singular' => $key,
			);

			if ( false !== strpos( $key, chr( 4 ) ) ) {
				$args['context']  = explode( chr( 4 ), $key )[0];
				$args['singular'] = explode( chr( 4 ), $key )[1];
			}

			if ( 2 === count( $value ) ) {
				$args['plural'] = $value[1];
			}

			$entries->add_entry( new Translation_Entry( $args ) );
		}

		return $entries;
	}

	/**
	 * Reads a set of translations from a JSON file.
	 *
	 * @since 0.1.0
	 *
	 * @param string     $file_name The name of the uploaded properties file.
	 * @param GP_Project $project   The project object to read the translations into.
	 * @return Translations
	 */
	public function read_translations_from_file( $file_name, $project = null ) {
		$entries = new Translations();

		$file = file_get_contents( $file_name );

		if ( ! $file ) {
			return $entries;
		}

		$json = $this->json_decode( $file );

		if ( ! $json ) {
			return $entries;
		}

		foreach ( $json['locale_data'][ $json['domain'] ] as $key => $value ) {
			if ( '' === $key ) {
				continue;
			}

			$args = array(
				'singular' => $key,
			);

			if ( false !== strpos( $key, chr( 4 ) ) ) {
				$args['context']  = explode( chr( 4 ), $key )[0];
				$args['singular'] = explode( chr( 4 ), $key )[1];
			}

			$args['translations'] = $value;

			$entries->add_entry( new Translation_Entry( $args ) );
		}

		return $entries;
	}

	/**
	 * Decodes a JSON string and checks for needed array keys.
	 *
	 * @since 0.1.0
	 *
	 * @param string $json The JSON string being decoded.
	 * @return array|false The encoded value or fals on failure.
	 */
	protected function json_decode( $json ) {
		$json = json_decode( $json, true );

		if ( null === $json ) {
			return false;
		}

		if ( ! isset( $json['domain'] ) ||
		     ! isset( $json['locale_data'] ) ||
		     ! isset( $json['locale_data'][ $json['domain'] ] )
		) {
			return false;
		}

		return $json;
	}
}
