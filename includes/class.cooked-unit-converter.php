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

use PhpUnitsOfMeasure\PhysicalQuantity\Volume;
use PhpUnitsOfMeasure\PhysicalQuantity\Mass;

/**
 * Cooked_Unit_Converter Class
 *
 * Converts ingredient amounts between measurement units using the
 * php-units-of-measure library.
 *
 * @since 1.15.0
 */
class Cooked_Unit_Converter {

	/**
	 * Maps Cooked unit keys to [PhysicalQuantity class, library unit name].
	 *
	 * @since 1.15.0
	 * @var array
	 */
	private static $unit_map = array(
		'cup'  => array( Volume::class, 'cup' ),
		'tsp'  => array( Volume::class, 'tsp' ),
		'tbsp' => array( Volume::class, 'tbsp' ),
		'floz' => array( Volume::class, 'fl oz' ),
		'gal'  => array( Volume::class, 'gal' ),
		'pt'   => array( Volume::class, 'pt' ),
		'qt'   => array( Volume::class, 'qt' ),
		'dl'   => array( Volume::class, 'dl' ),
		'ml'   => array( Volume::class, 'ml' ),
		'l'    => array( Volume::class, 'l' ),
		'oz'   => array( Mass::class, 'oz' ),
		'lb'   => array( Mass::class, 'lb' ),
		'g'    => array( Mass::class, 'g' ),
		'kg'   => array( Mass::class, 'kg' ),
		'mg'   => array( Mass::class, 'mg' ),
	);

	/**
	 * Default opposite-system target for each unit.
	 *
	 * @since 1.15.0
	 * @var array
	 */
	private static $default_targets = array(
		'cup'  => 'ml',
		'tsp'  => 'ml',
		'tbsp' => 'ml',
		'floz' => 'ml',
		'gal'  => 'l',
		'pt'   => 'ml',
		'qt'   => 'ml',
		'dl'   => 'floz',
		'ml'   => 'floz',
		'l'    => 'cup',
		'oz'   => 'g',
		'lb'   => 'g',
		'g'    => 'oz',
		'kg'   => 'lb',
		'mg'   => 'g',
	);

	/**
	 * Convert an amount from one unit to another.
	 *
	 * @since 1.15.0
	 * @param float  $amount The numeric amount to convert.
	 * @param string $from   The measurement key to convert from.
	 * @param string $to     The target measurement key.
	 * @return array|null Array with 'amount' (float) and 'unit' (string), or null.
	 */
	public static function convert( $amount, $from, $to ) {
		if ( ! isset( self::$unit_map[ $from ], self::$unit_map[ $to ] ) ) {
			return null;
		}

		list( $from_class, $from_unit ) = self::$unit_map[ $from ];
		list( $to_class, $to_unit )     = self::$unit_map[ $to ];

		if ( $from_class !== $to_class ) {
			return null;
		}

		return array(
			'amount' => ( new $from_class( $amount, $from_unit ) )->toUnit( $to_unit ),
			'unit'   => $to,
		);
	}

	/**
	 * Get the default opposite-system target unit for a given source unit.
	 *
	 * @since 1.15.0
	 * @param string $from_unit_key The measurement key to convert from.
	 * @return string|null The target unit key, or null if no conversion exists.
	 */
	public static function get_target_unit( $from_unit_key ) {
		if ( ! isset( self::$default_targets[ $from_unit_key ] ) ) {
			return null;
		}

		return self::$default_targets[ $from_unit_key ];
	}
}
