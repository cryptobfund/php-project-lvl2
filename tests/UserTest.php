<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;

use function Gendiff\Analyzer\genDiff;

class UserTest extends TestCase
{
    private string $path = __DIR__ . "/fixtures/";

    /**
     * @dataProvider genDiffProvider
     */
    public function testGenDiff($before, $after, $format, $expected)
    {
        $this->assertStringEqualsFile(
            $this->path . $expected,
            genDiff($this->path . $before, $this->path . $after, $format)
        );
    }

    public function genDiffProvider()
    {
        return [
            'testYamlPretty' => ['before.yaml', 'after.yaml', 'pretty', 'prettyFlatExpected'],
            'testJsonNestedPretty' => ['beforeNested.json', 'afterNested.json', 'pretty', 'prettyExpected'],
            'testJsonNestedPlain' => ['beforeNested.json', 'afterNested.json', 'plain', 'plainExpected'],
            'testJsonNestedJson' => ['beforeNested.json', 'afterNested.json', 'json', 'jsonExpected'],
        ];
    }
}
