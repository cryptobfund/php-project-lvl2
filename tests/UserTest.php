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
            $this->getFilePath($expected),
            genDiff($this->getFilePath($before), $this->getFilePath($after), $format)
        );
    }

    public function genDiffProvider()
    {
        return [
            'testYamlPretty' => ['before.yaml', 'after.yaml', 'pretty', 'prettyExpected'],
            'testYmlPlain' => ['before.yml', 'after.yml', 'plain', 'plainExpected'],
            'testJsonPretty' => ['before.json', 'after.json', 'pretty', 'prettyExpected'],
            'testJsonPlain' => ['before.json', 'after.json', 'plain', 'plainExpected'],
            'testJsonJson' => ['before.json', 'after.json', 'json', 'jsonExpected'],
        ];
    }
    private function getFilePath($fileName)
    {
        return $this->path . $fileName;
    }
}
