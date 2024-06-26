<?php

namespace Bookit\Helpers;

/**
 * Bookit Clean Helper
 */


class CleanHelper {

	/**
	 * @param array $data
	 * @param array $rules
	 *
	 * @return array|null[]
	 * Clean plugin data by rules for each field
	 */
	public static function cleanData( array $data, array $rules ) {
		$data = array_map(
			function ( $value ) {
				return ( 'null' == $value ) ? null : $value;
			},
			$data
		);

		foreach ( $data as $key => $value ) {
			$value = sanitize_text_field( $value );

			if ( ! array_key_exists( $key, $rules ) ) {
				continue;
			}

			// convert to correct type
			if ( array_key_exists( 'type', $rules[ $key ] ) ) {
				$setTypeFunction = $rules[ $key ]['type'];
				$value           = $setTypeFunction( $value );
			}

			// apply function to clean
			if ( array_key_exists( 'function', $rules[ $key ] ) ) {

				if ( ! $rules[ $key ]['function']['custom'] ) {
					$value = $rules[ $key ]['function']['name']( $value );
				}

				if ( $rules[ $key ]['function']['custom']
					&& method_exists( self::class, $rules[ $key ]['function']['name'] ) ) {
					$value = self::{$rules[ $key ]['function']['name']}( $value );
				}
			}
			$data[ $key ] = $value;
		}

		return $data;
	}

	protected static function custom_sanitize_phone( string $phone ) {
		if ( ! $phone ) {
			return $phone;
		}
		$phone = filter_var( $phone, FILTER_SANITIZE_NUMBER_INT );
		$phone = str_replace( '-', '', $phone );

		return $phone;
	}

	protected static function custom_sanitize_json( string $json ) {
		return json_decode( stripslashes( $json ), true );
	}

	protected static function custom_sanitize_price( string $price ) {
		return number_format( $price, 2, '.', '' );
	}

	/**
	 * @param string $ids
	 * check is string contain comma separated ids
	 */
	protected static function custom_comma_separated_ids( string $ids_string ) {
		$ids = array_filter(
			explode( ',', $ids_string ),
			function( $value ) {
				return ! filter_var( $value, FILTER_VALIDATE_INT ) === false;
			}
		);
		return implode( ',', $ids );
	}
}
