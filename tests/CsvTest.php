<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Spyck\Spreadsheet\Csv;
use Spyck\Spreadsheet\Exception\FieldCountException;
use Spyck\Spreadsheet\Exception\FieldRequiredException;

final class CsvTest extends TestCase
{
    private Csv $csv;

    public function setUp(): void
    {
        $this->csv = new Csv();
    }

    public function testGetCsv(): void
    {
        $content = 'Field1;Field2;Field3';
        $content .= PHP_EOL;
        $content .= 'value1;value2 ; value3';
        $content .= PHP_EOL;
        $content .= 'value4;value5;value6';

        $fields = [
            'Field1' => 'field1',
            'Field2' => 'field2',
            'Field3' => 'field3',
        ];

        $this->csv->setDelimiter(';');

        $returnData = $this->csv->getResult(sprintf('data://text/plain,%s', $content), $fields)->getData();

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

    /**
     * Test CSV with empty line.
     */
    public function testGetCsvWithEmptyLine(): void
    {
        $content = 'Field1;Field2;Field3';
        $content .= PHP_EOL;
        $content .= 'value1;value2 ; value3';
        $content .= PHP_EOL;
        $content .= 'value4;value5;value6';
        $content .= PHP_EOL;

        $fields = [
            'Field1' => 'field1',
            'Field2' => 'field2',
            'Field3' => 'field3',
        ];

        $this->csv->setDelimiter(';');

        $returnData = $this->csv->getResult(sprintf('data://text/plain,%s', $content), $fields)->getData();

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

    /**
     * Test CSV without header.
     */
    public function testGetCsvWithoutHeader(): void
    {
        $content = 'value1,value2,value3';
        $content .= PHP_EOL;
        $content .= 'value4,value5,value6';

        $fields = [
            0 => 'field1',
            1 => 'field2',
            2 => 'field3',
        ];

        $this->csv->setHeader(null);

        $returnData = $this->csv->getResult(sprintf('data://text/plain,%s', $content), $fields)->getData();

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

    /**
     * Test CSV with field count.
     */
    public function testGetCsvWithFieldCount(): void
    {
        $content = 'Field1,Field2,Field3';
        $content .= PHP_EOL;
        $content .= 'value1,value2,value3,value4,value5';
        $content .= PHP_EOL;
        $content .= 'value6,value7,value8,value9';

        $fields = [
            'Field1' => 'field1',
            'Field2' => 'field2',
            'Field3' => 'field3',
        ];

        $returnData = $this->csv->getResult(sprintf('data://text/plain,%s', $content), $fields)->getData();

        self::assertSame([
            [
                'field1' => 'value1',
                'field2' => 'value2',
                'field3' => 'value3',
            ],
            [
                'field1' => 'value6',
                'field2' => 'value7',
                'field3' => 'value8',
            ],
        ], $returnData);
    }

    /**
     * Test CSV with incorrect fields count.
     */
    public function testGetCsvWithFieldCountError(): void
    {
        $content = 'Field1,Field2,Field3';
        $content .= PHP_EOL;
        $content .= 'value1,value2';

        $fields = [
            'Field1' => 'field1',
            'Field2' => 'field2',
            'Field3' => 'field3',
        ];

        self::expectException(FieldCountException::class);
        self::expectExceptionMessage('Incorrect field count on line 2 (3 instead of 2)');

        $this->csv->getResult(sprintf('data://text/plain,%s', $content), $fields)->getData();
    }

    public function testGetCsvWithFieldCountErrorDisabled(): void
    {
        $content = 'Field1,Field2,Field3';
        $content .= PHP_EOL;
        $content .= 'value1,value2';

        $fields = [
            'Field1' => 'field1',
            'Field2' => 'field2',
            'Field3' => 'field3',
        ];

        $this->csv->setCheck(false);

        $returnData = $this->csv->getResult(sprintf('data://text/plain,%s', $content), $fields)->getData();

        self::assertSame([
            [
                'field1' => 'value1',
                'field2' => 'value2',
                'field3' => null,
            ],
        ], $returnData);
    }

    /**
     * Test CSV with missing required fields.
     */
    public function testGetCsvWithFieldError(): void
    {
        $content = 'Field1,Field3';
        $content .= PHP_EOL;
        $content .= 'value1,value3';

        $fields = [
            'Field1' => 'field1',
            'Field2' => 'field2',
            'Field3' => 'field3',
        ];

        self::expectException(FieldRequiredException::class);
        self::expectExceptionMessage('Missing required fields (Field2) (Field1, Field3)');

        $this->csv->getResult(sprintf('data://text/plain,%s', $content), $fields)->getData();
    }

    /**
     * Test CSV with filter.
     */
    public function testGetCsvWithFilter(): void
    {
        $content = 'Field1,Field2';
        $content .= PHP_EOL;
        $content .= 'value1,value2';
        $content .= PHP_EOL;
        $content .= 'value3,value4';
        $content .= PHP_EOL;
        $content .= 'value5,value6';

        $fields = [
            'Field1' => 'field1',
            'Field2' => 'field2',
        ];

        $this->csv->setFilter(function (array $data): bool {
            return in_array($data['field2'], ['value4', 'value6'], true);
        });

        $returnData = $this->csv->getResult(sprintf('data://text/plain,%s', $content), $fields)->getData();

        self::assertSame([
            [
                'field1' => 'value3',
                'field2' => 'value4',
            ],
            [
                'field1' => 'value5',
                'field2' => 'value6',
            ],
        ], $returnData);
    }

    /**
     * Test CSV with EOF check.
     */
    public function testGetCsvWithEof(): void
    {
        $content = 'Field1,Field2';
        $content .= PHP_EOL;
        $content .= 'value1,value2';
        $content .= PHP_EOL;
        $content .= 'value3,value4';
        $content .= PHP_EOL;
        $content .= 'value5,value6';
        $content .= PHP_EOL;
        $content .= 'Count,3';
        $content .= PHP_EOL;
        $content .= 'value7,value8';

        $fields = [
            'Field1' => 'field1',
            'Field2' => 'field2',
        ];

        $this->csv->setEof(function ($data, $count): bool {
            return 'Count' === $data[0] && $data[1] === (string) $count;
        });

        $returnData = $this->csv->getResult(sprintf('data://text/plain,%s', $content), $fields)->getData();

        self::assertSame([
            [
                'field1' => 'value1',
                'field2' => 'value2',
            ],
            [
                'field1' => 'value3',
                'field2' => 'value4',
            ],
            [
                'field1' => 'value5',
                'field2' => 'value6',
            ],
        ], $returnData);
    }
}
