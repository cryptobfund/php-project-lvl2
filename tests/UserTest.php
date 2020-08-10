<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;

use function Gendiff\Analyzer\genDiff;

class UserTest extends TestCase
{
    private string $path = __DIR__ . "/fixtures/";
    private string $beforeNestedJson = __DIR__ . "/fixtures/beforeNested.json";
    private string $afterNestedJson = __DIR__ . "/fixtures/afterNested.json";
    private string $beforeYaml = __DIR__ . "/fixtures/before.yaml";
    private string $afterYaml = __DIR__ . "/fixtures/after.yaml";

    public function testYamlPretty()
    {
        $this->assertEquals(
            file_get_contents($this->path . "prettyFlatExpected"),
            genDiff($this->beforeYaml, $this->afterYaml, 'pretty')
        );
    }

    public function testJsonNestedPretty()
    {
        $this->assertEquals(
            file_get_contents($this->path . "prettyExpected"),
            genDiff($this->beforeNestedJson, $this->afterNestedJson, 'pretty')
        );
    }

    public function testJsonNestedPlain()
    {
        $this->assertEquals(
            file_get_contents($this->path . "plainExpected"),
            genDiff($this->beforeNestedJson, $this->afterNestedJson, 'plain')
        );
    }

    public function testJsonNestedJson()
    {
        $this->assertEquals(
            file_get_contents($this->path . "jsonExpected"),
            genDiff($this->beforeNestedJson, $this->afterNestedJson, 'json')
        );
    }
}
