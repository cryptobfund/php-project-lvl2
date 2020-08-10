<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;

use function Gendiff\Analyzer\genDiff;

class UserTest extends TestCase
{
    private string $expected = <<<DOC
{
    host: hexlet.io
  - timeout: 50
  + timeout: 20
  - proxy: 123.234.53.22
  + verbose: true
}
DOC;

    public function testJsonPretty()
    {
        $pathBefore = __DIR__ . "/fixtures/before.json";
        $pathAfter = __DIR__ . "/fixtures/after.json";
        $this->assertEquals($this->expected, genDiff($pathBefore, $pathAfter, "pretty"));
    }

    public function testYamlPretty()
    {
        $pathBefore = __DIR__ . "/fixtures/before.yaml";
        $pathAfter = __DIR__ . "/fixtures/after.yaml";
        $this->assertEquals($this->expected, genDiff($pathBefore, $pathAfter, "pretty"));
    }

    private string $expectedNested = <<<DOC
{
    common: {
        setting1: Value 1
      - setting2: 200
        setting3: true
      - setting6: {
            key: value
        }
      + setting4: blah blah
      + setting5: {
            key5: value5
        }
    }
    group1: {
      - baz: bas
      + baz: bars
        foo: bar
    }
  - group2: {
        abc: 12345
    }
  + group3: {
        fee: 100500
    }
}
DOC;

    public function testJsonNestedPretty()
    {
        $pathBefore = __DIR__ . "/fixtures/beforeNested.json";
        $pathAfter = __DIR__ . "/fixtures/afterNested.json";
        $this->assertEquals($this->expectedNested, genDiff($pathBefore, $pathAfter, 'pretty'));
    }

    private string $expectedNestedPlain = <<<DOC
Property 'common.setting2' was removed
Property 'common.setting6' was removed
Property 'common.setting4' was added with value: 'blah blah'
Property 'common.setting5' was added with value: 'complex value'
Property 'group1.baz' was changed. From 'bas' to 'bars'
Property 'group2' was removed
Property 'group3' was added with value: 'complex value'

DOC;

    public function testJsonNestedPlain()
    {
        $pathBefore = __DIR__ . "/fixtures/beforeNested.json";
        $pathAfter = __DIR__ . "/fixtures/afterNested.json";
        $this->assertEquals($this->expectedNestedPlain, genDiff($pathBefore, $pathAfter, 'plain'));
    }
}
