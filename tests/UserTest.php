<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;

use function Gendiff\Analyzer\genDiff;

class UserTest extends TestCase
{
    public function testPlainJson()
    {
        $pathBefore = __DIR__ . "/fixtures/before.json";
        $pathAfter = __DIR__ . "/fixtures/after.json";
        $result = <<<DOC
{
    host: hexlet.io
  - proxy: 123.234.53.22
  + verbose: 1
  - timeout: 50
  + timeout: 20
}
DOC;
        $this->assertEquals($result, genDiff('plain', $pathBefore, $pathAfter));
    }
}
