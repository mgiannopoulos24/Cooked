<?php

use PHPUnit\Framework\TestCase;

class UpdatesTest extends TestCase {

    public function test_get_runnable_tools_returns_array() {
        $tools = Cooked_Updates::get_runnable_tools();
        $this->assertIsArray($tools);
    }

    public function test_get_runnable_tools_has_tools() {
        $tools = Cooked_Updates::get_runnable_tools();
        $this->assertNotEmpty($tools);
    }

    public function test_get_runnable_tools_tool_has_required_keys() {
        $tools = Cooked_Updates::get_runnable_tools();
        foreach ($tools as $tool) {
            $this->assertArrayHasKey('id', $tool);
            $this->assertArrayHasKey('name', $tool);
        }
    }

    public function test_run_tool_returns_error_for_empty_tool_name() {
        $result = Cooked_Updates::run_tool('');
        $this->assertInstanceOf(WP_Error::class, $result);
    }

    public function test_run_tool_returns_error_for_unknown_tool() {
        $result = Cooked_Updates::run_tool('nonexistent_tool');
        $this->assertInstanceOf(WP_Error::class, $result);
    }
}
