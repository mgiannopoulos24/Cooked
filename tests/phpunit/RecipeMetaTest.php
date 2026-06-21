<?php

use PHPUnit\Framework\TestCase;

class RecipeMetaTest extends TestCase {

    public function test_meta_cleanup_empty_input() {
        $result = Cooked_Recipe_Meta::meta_cleanup([]);
        $this->assertSame([], $result);
    }

    public function test_meta_cleanup_simple_field() {
        $input = ['title' => 'My Recipe'];
        $result = Cooked_Recipe_Meta::meta_cleanup($input);
        $this->assertArrayHasKey('title', $result);
    }

    public function test_meta_cleanup_strips_tags_from_non_content() {
        $input = ['title' => '<script>alert("xss")</script>My Recipe'];
        $result = Cooked_Recipe_Meta::meta_cleanup($input);
        $this->assertStringNotContainsString('<script>', $result['title']);
    }

    public function test_meta_cleanup_processes_nested_directions() {
        $input = [
            'directions' => [
                ['content' => 'Mix ingredients'],
                ['section_heading_name' => 'Step 1'],
            ]
        ];
        $result = Cooked_Recipe_Meta::meta_cleanup($input);
        $this->assertArrayHasKey('directions', $result);
    }

    public function test_meta_cleanup_processes_deeply_nested_arrays() {
        $input = [
            'ingredients' => [
                'rand1' => [
                    'amount' => '2',
                    'measurement' => 'cups',
                    'name' => 'Flour',
                ]
            ]
        ];
        $result = Cooked_Recipe_Meta::meta_cleanup($input);
        $this->assertArrayHasKey('ingredients', $result);
    }
}
