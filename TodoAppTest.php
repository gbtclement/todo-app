<?php

require_once 'TodoApp.php';


class TestCase
{
    protected function assertEquals($expected, $actual, $message = '')
    {
        if ($expected !== $actual) {
            throw new Exception("Assertion failed: Expected '$expected', got '$actual'. $message");
        }
        echo "✅ Test passed: $message\n";
    }

    protected function assertCount($expected, $array, $message = '')
    {
        if (count($array) !== $expected) {
            throw new Exception("Assertion failed: Expected count $expected, got " . count($array) . ". $message");
        }
        echo "✅ Test passed: $message\n";
    }

    protected function assertTrue($condition, $message = '')
    {
        if (!$condition) {
            throw new Exception("Assertion failed: Expected true. $message");
        }
        echo "✅ Test passed: $message\n";
    }

    protected function assertFalse($condition, $message = '')
    {
        if ($condition) {
            throw new Exception("Assertion failed: Expected false. $message");
        }
        echo "✅ Test passed: $message\n";
    }

    protected function assertNull($value, $message = '')
    {
        if ($value !== null) {
            throw new Exception("Assertion failed: Expected null. $message");
        }
        echo "✅ Test passed: $message\n";
    }

    protected function assertNotNull($value, $message = '')
    {
        if ($value === null) {
            throw new Exception("Assertion failed: Expected not null. $message");
        }
        echo "✅ Test passed: $message\n";
    }

    protected function assertNotEmpty($value, $message = '')
    {
        if (empty($value)) {
            throw new Exception("Assertion failed: Expected not empty. $message");
        }
        echo "✅ Test passed: $message\n";
    }

    protected function expectException($exception)
    {
        // Simple implementation pour les tests de base
    }

    protected function expectExceptionMessage($message)
    {
        // Simple implementation pour les tests de base
    }

    protected function setUp(): void
    {
        // Override in child class
    }
}
