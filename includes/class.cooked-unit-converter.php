<?php
/**
 * Cooked Unit Converter
 *
 * @package     Cooked
 * @subpackage  Unit Converter
 * @since       1.15.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cooked_Unit_Converter Class
 *
 * Converts ingredient amounts between metric and imperial systems.
 *
 * @since 1.15.0
 */
class Cooked_Unit_Converter {

	/**
	 * Conversion table mapping each unit to its opposite-system equivalent.
	 *
	 * @since 1.15.0
	 * @var array
	 */
	private static $table = array(
		'cup'  => array(
			'to'     => 'ml',
			'factor' => 236.588,
		),
		'tsp'  => array(
			'to'     => 'ml',
			'factor' => 4.929,
		),
		'tbsp' => array(
			'to'     => 'ml',
			'factor' => 14.787,
		),
		'floz' => array(
			'to'     => 'ml',
			'factor' => 29.574,
		),
		'gal'  => array(
			'to'     => 'l',
			'factor' => 3.785,
		),
		'pt'   => array(
			'to'     => 'ml',
			'factor' => 473.176,
		),
		'qt'   => array(
			'to'     => 'ml',
			'factor' => 946.353,
		),
		'dl'   => array(
			'to'     => 'floz',
			'factor' => 3.381,
		),
		'ml'   => array(
			'to'     => 'floz',
			'factor' => 0.034,
		),
		'l'    => array(
			'to'     => 'cup',
			'factor' => 4.227,
		),
		'oz'   => array(
			'to'     => 'g',
			'factor' => 28.3495,
		),
		'lb'   => array(
			'to'     => 'g',
			'factor' => 453.592,
		),
		'g'    => array(
			'to'     => 'oz',
			'factor' => 0.0353,
		),
		'kg'   => array(
			'to'     => 'lb',
			'factor' => 2.205,
		),
		'mg'   => array(
			'to'     => 'g',
			'factor' => 0.001,
		),
	);

	/**
	 * Convert an amount from one unit to its opposite-system equivalent.
	 *
	 * @since 1.15.0
	 * @param float  $amount        The numeric amount to convert.
	 * @param string $from_unit_key The measurement key to convert from (e.g. 'cup', 'oz', 'g').
	 * @return array|null  Array with 'amount' (float) and 'unit' (string) on success, null if no conversion exists.
	 */
	public static function convert( $amount, $from_unit_key ) {
		if ( ! isset( self::$table[ $from_unit_key ] ) ) {
			return null;
		}

		$target = self::$table[ $from_unit_key ];

		return array(
			'amount' => $amount * $target['factor'],
			'unit'   => $target['to'],
		);
	}

	/**
	 * Get the target unit key for a given source unit.
	 *
	 * @since 1.15.0
	 * @param string $from_unit_key The measurement key to convert from.
	 * @return string|null  The target unit key, or null if no conversion exists.
	 */
	public static function get_target_unit( $from_unit_key ) {
		if ( ! isset( self::$table[ $from_unit_key ] ) ) {
			return null;
		}

		return self::$table[ $from_unit_key ]['to'];
	}
}
