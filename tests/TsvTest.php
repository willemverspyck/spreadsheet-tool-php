<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Spyck\Spreadsheet\Tsv;

final class TsvTest extends TestCase
{
    private Tsv $tsv;

    public function setUp(): void
    {
        $this->tsv = new Tsv();
    }

    public function testGetCsv(): void
    {
        $content = 'Field1'."\t".'Field2'."\t".'Field3';
        $content .= PHP_EOL;
        $content .= 'value1'."\t".'value2 '."\t".' value3';
        $content .= PHP_EOL;
        $content .= 'value4'."\t".'value5'."\t".'value6';

        $fields = [
            'Field1' => 'field1',
            'Field2' => 'field2',
            'Field3' => 'field3',
        ];

        $returnData = $this->tsv->getResult(sprintf('data://text/plain,%s', $content), $fields)->getData();

        self::assertSame([
            [
                'field1' => 'value1',
                'field2' => 'value2',
                'field3' => 'value3',
            ],
            [
                'field1' => 'value4',
                'field2' => 'value5',
                'field3' => 'value6',
            ],
        ], $returnData);
    }
}
