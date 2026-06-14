<?php
/**
 * Tests for Cooked_Unit_Converter class.
 *
 * @package Cooked
 * @subpackage Tests
 */

use PHPUnit\Framework\TestCase;

class UnitConverterTest extends TestCase {

    /**
     * Test volume conversions from imperial to metric.
     *
     * @dataProvider data_volume_imperial_to_metric
     */
    public function test_volume_imperial_to_metric( $from, $to, $amount, $expected_amount, $tolerance ) {
        $result = Cooked_Unit_Converter::convert( $amount, $from );
        $this->assertNotNull( $result );
        $this->assertSame( $to, $result['unit'] );
        $this->assertEqualsWithDelta( $expected_amount, $result['amount'], $tolerance );
    }

    public function data_volume_imperial_to_metric() {
        return array(
            'cup to ml'  => array( 'cup', 'ml', 1, 236.588, 0.001 ),
            'tsp to ml'  => array( 'tsp', 'ml', 1, 4.929, 0.001 ),
            'tbsp to ml' => array( 'tbsp', 'ml', 1, 14.787, 0.001 ),
            'floz to ml' => array( 'floz', 'ml', 1, 29.574, 0.001 ),
            'gal to l'   => array( 'gal', 'l', 1, 3.785, 0.001 ),
            'pt to ml'   => array( 'pt', 'ml', 1, 473.176, 0.001 ),
            'qt to ml'   => array( 'qt', 'ml', 1, 946.353, 0.001 ),
        );
    }

    /**
     * Test volume conversions from metric to imperial.
     *
     * @dataProvider data_volume_metric_to_imperial
     */
    public function test_volume_metric_to_imperial( $from, $to, $amount, $expected_amount, $tolerance ) {
        $result = Cooked_Unit_Converter::convert( $amount, $from );
        $this->assertNotNull( $result );
        $this->assertSame( $to, $result['unit'] );
        $this->assertEqualsWithDelta( $expected_amount, $result['amount'], $tolerance );
    }

    public function data_volume_metric_to_imperial() {
        return array(
            'dl to floz'   => array( 'dl', 'floz', 1, 3.381, 0.001 ),
            'ml to floz'   => array( 'ml', 'floz', 1, 0.034, 0.001 ),
            'l to cup'     => array( 'l', 'cup', 1, 4.227, 0.001 ),
        );
    }

    /**
     * Test weight conversions from imperial to metric.
     *
     * @dataProvider data_weight_imperial_to_metric
     */
    public function test_weight_imperial_to_metric( $from, $to, $amount, $expected_amount, $tolerance ) {
        $result = Cooked_Unit_Converter::convert( $amount, $from );
        $this->assertNotNull( $result );
        $this->assertSame( $to, $result['unit'] );
        $this->assertEqualsWithDelta( $expected_amount, $result['amount'], $tolerance );
    }

    public function data_weight_imperial_to_metric() {
        return array(
            'oz to g' => array( 'oz', 'g', 1, 28.3495, 0.001 ),
            'lb to g' => array( 'lb', 'g', 1, 453.592, 0.001 ),
        );
    }

    /**
     * Test weight conversions from metric to imperial.
     *
     * @dataProvider data_weight_metric_to_imperial
     */
    public function test_weight_metric_to_imperial( $from, $to, $amount, $expected_amount, $tolerance ) {
        $result = Cooked_Unit_Converter::convert( $amount, $from );
        $this->assertNotNull( $result );
        $this->assertSame( $to, $result['unit'] );
        $this->assertEqualsWithDelta( $expected_amount, $result['amount'], $tolerance );
    }

    public function data_weight_metric_to_imperial() {
        return array(
            'g to oz'  => array( 'g', 'oz', 1, 0.0353, 0.001 ),
            'kg to lb' => array( 'kg', 'lb', 1, 2.205, 0.001 ),
            'mg to g'  => array( 'mg', 'g', 1, 0.001, 0.0001 ),
        );
    }

    /**
     * Test get_target_unit returns correct target keys.
     *
     * @dataProvider data_target_units
     */
    public function test_get_target_unit( $from, $expected_target ) {
        $this->assertSame( $expected_target, Cooked_Unit_Converter::get_target_unit( $from ) );
    }

    public function data_target_units() {
        return array(
            array( 'cup', 'ml' ),
            array( 'tsp', 'ml' ),
            array( 'tbsp', 'ml' ),
            array( 'floz', 'ml' ),
            array( 'gal', 'l' ),
            array( 'pt', 'ml' ),
            array( 'qt', 'ml' ),
            array( 'dl', 'floz' ),
            array( 'ml', 'floz' ),
            array( 'l', 'cup' ),
            array( 'oz', 'g' ),
            array( 'lb', 'g' ),
            array( 'g', 'oz' ),
            array( 'kg', 'lb' ),
            array( 'mg', 'g' ),
        );
    }

    /**
     * Test that neutral units return null.
     */
    public function test_neutral_units_return_null() {
        $neutral_units = array( 'stick', 'dash', 'drop', 'pinch', 'drizzle', 'clove', 'jar', 'can' );
        foreach ( $neutral_units as $unit ) {
            $this->assertNull( Cooked_Unit_Converter::convert( 1, $unit ) );
            $this->assertNull( Cooked_Unit_Converter::get_target_unit( $unit ) );
        }
    }

    /**
     * Test unknown units return null.
     */
    public function test_unknown_unit_returns_null() {
        $this->assertNull( Cooked_Unit_Converter::convert( 1, 'nonexistent' ) );
        $this->assertNull( Cooked_Unit_Converter::get_target_unit( 'nonexistent' ) );
    }

    /**
     * Test converting zero amount.
     */
    public function test_convert_zero_amount() {
        $result = Cooked_Unit_Converter::convert( 0, 'cup' );
        $this->assertNotNull( $result );
        $this->assertSame( 'ml', $result['unit'] );
        $this->assertEquals( 0, $result['amount'] );
    }

    /**
     * Test converting fractional amounts.
     */
    public function test_convert_fractional_amount() {
        $result = Cooked_Unit_Converter::convert( 0.5, 'cup' );
        $this->assertNotNull( $result );
        $this->assertSame( 'ml', $result['unit'] );
        $this->assertEqualsWithDelta( 118.294, $result['amount'], 0.001 );
    }

    /**
     * Test converting large amounts.
     */
    public function test_convert_large_amount() {
        $result = Cooked_Unit_Converter::convert( 10, 'oz' );
        $this->assertNotNull( $result );
        $this->assertSame( 'g', $result['unit'] );
        $this->assertEqualsWithDelta( 283.495, $result['amount'], 0.001 );
    }


}
