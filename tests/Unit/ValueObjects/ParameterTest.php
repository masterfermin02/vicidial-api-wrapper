<?php

declare(strict_types=1);

namespace Tests\Unit\ValueObjects;

use PHPUnit\Framework\TestCase;
use VicidialApi\ValueObjects\Parameter;

/**
 * Unit tests for Parameter value object
 * Tests parameter encoding and URL-encode behavior
 */
final class ParameterTest extends TestCase
{
    public function test_converts_string_to_string(): void
    {
        $param = Parameter::from('test_value');

        $this->assertEquals('test_value', $param->toString());
    }

    public function test_converts_integer_to_string(): void
    {
        $param = Parameter::from(12345);

        $this->assertEquals('12345', $param->toString());
    }

    public function test_converts_float_to_string(): void
    {
        $param = Parameter::from(123.45);

        $this->assertEquals('123.45', $param->toString());
    }

    public function test_converts_boolean_true_to_string(): void
    {
        $param = Parameter::from(true);

        $this->assertEquals('1', $param->toString());
    }

    public function test_converts_boolean_false_to_string(): void
    {
        $param = Parameter::from(false);

        $this->assertEquals('', $param->toString());
    }

    public function test_converts_null_to_empty_string(): void
    {
        $param = Parameter::from(null);

        $this->assertEquals('', $param->toString());
    }

    public function test_converts_array_to_comma_separated_string(): void
    {
        $param = Parameter::fromArray(['value1', 'value2', 'value3']);

        $this->assertEquals('value1,value2,value3', $param->toString());
    }

    public function test_converts_numeric_array_to_comma_separated_string(): void
    {
        $param = Parameter::fromArray([100, 200, 300]);

        $this->assertEquals('100,200,300', $param->toString());
    }

    public function test_converts_empty_array_to_empty_string(): void
    {
        $param = Parameter::fromArray([]);

        $this->assertEquals('', $param->toString());
    }

    public function test_converts_object_to_json(): void
    {
        $obj = new \stdClass;
        $obj->name = 'test';
        $obj->value = 123;

        $param = Parameter::fromObject($obj);

        $this->assertEquals('{"name":"test","value":123}', $param->toString());
    }

    public function test_handles_special_characters_in_string(): void
    {
        $param = Parameter::from("O'Brien & Sons");

        $this->assertEquals("O'Brien & Sons", $param->toString());
    }

    public function test_handles_unicode_characters(): void
    {
        $param = Parameter::from('Café München');

        $this->assertEquals('Café München', $param->toString());
    }

    public function test_handles_newlines_and_tabs(): void
    {
        $param = Parameter::from("Line1\nLine2\tTab");

        $this->assertEquals("Line1\nLine2\tTab", $param->toString());
    }

    public function test_handles_url_like_strings(): void
    {
        $param = Parameter::from('https://example.com?test=value');

        $this->assertEquals('https://example.com?test=value', $param->toString());
    }

    public function test_from_accepts_array_and_converts(): void
    {
        $param = Parameter::from(['a', 'b', 'c']);

        $this->assertEquals('a,b,c', $param->toString());
    }

    public function test_from_accepts_object_and_converts(): void
    {
        $obj = new \stdClass;
        $obj->status = 'active';

        $param = Parameter::from($obj);

        $this->assertEquals('{"status":"active"}', $param->toString());
    }

    public function test_nested_array_conversion(): void
    {
        // Nested arrays should be flattened via implode
        $param = Parameter::from(['campaign1', 'campaign2']);

        $this->assertEquals('campaign1,campaign2', $param->toString());
    }

    public function test_handles_zero_value(): void
    {
        $param = Parameter::from(0);

        $this->assertEquals('0', $param->toString());
    }

    public function test_handles_empty_string(): void
    {
        $param = Parameter::from('');

        $this->assertEquals('', $param->toString());
    }
}
