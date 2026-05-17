<?php

use PHPUnit\Framework\TestCase;

class RelatedRecipesTest extends TestCase {

    public function test_get_default_atts_returns_array() {
        $atts = Cooked_Related_Recipes::get_default_atts();
        $this->assertIsArray($atts);
    }

    public function test_get_default_atts_has_required_keys() {
        $atts = Cooked_Related_Recipes::get_default_atts();
        $this->assertArrayHasKey('id', $atts);
        $this->assertArrayHasKey('limit', $atts);
        $this->assertArrayHasKey('columns', $atts);
        $this->assertArrayHasKey('hide_image', $atts);
        $this->assertArrayHasKey('match_categories', $atts);
    }

    public function test_get_default_atts_expected_values() {
        $atts = Cooked_Related_Recipes::get_default_atts();
        $this->assertFalse($atts['id']);
        $this->assertSame(4, $atts['limit']);
        $this->assertSame(2, $atts['columns']);
        $this->assertFalse($atts['hide_image']);
        $this->assertTrue($atts['match_categories']);
    }

    public function test_get_default_atts_title_is_string() {
        $atts = Cooked_Related_Recipes::get_default_atts();
        $this->assertIsString($atts['title']);
        $this->assertNotEmpty($atts['title']);
    }
}
