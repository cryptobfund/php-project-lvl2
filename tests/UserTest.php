<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;

use function Gendiff\Analyzer\genDiff;

class UserTest extends TestCase
{
    private string $expected = <<<DOC
{
    host: hexlet.io
  - proxy: 123.234.53.22
  + verbose: 1
  - timeout: 50
  + timeout: 20
}
DOC;

    public function testPlainJson()
    {
        $pathBefore = __DIR__ . "/fixtures/before.json";
        $pathAfter = __DIR__ . "/fixtures/after.json";
        $this->assertEquals($this->expected, genDiff('plain', $pathBefore, $pathAfter));
    }

    public function testPlainYaml()
    {
        $pathBefore = __DIR__ . "/fixtures/before.yaml";
        $pathAfter = __DIR__ . "/fixtures/after.yaml";
        $this->assertEquals($this->expected, genDiff('plain', $pathBefore, $pathAfter));
    }
}
