<?php

namespace Tests\Integration;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_something_complex(): void
    {
        $object = [
            'key' => 'value',
            'number' => 42,
        ];

        $this->assertEquals('value', $object['key']);
        $this->assertEquals(42, $object['number']);
    }
}
