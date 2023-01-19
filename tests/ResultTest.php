<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Spyck\Spreadsheet\Result;

final class ResultTest extends TestCase
{
    private Result $result;

    public function setUp(): void
    {
        $this->result = new Result();
        $this->result->setCount(2);
        $this->result->setCountRow(10);
        $this->result->setData([
            [
                'name' => 'apple',
                'count' => '10',
            ],
            [
                'name' => 'banana',
                'count' => '20',
            ],
        ]);
    }

    public function testGetCount(): void
    {
        self::assertEquals(2, $this->result->getCount());
    }

    public function testGetCountRow(): void
    {
        self::assertEquals(10, $this->result->getCountRow());
    }

    public function testGetData(): void
    {
        self::assertEquals([
            [
                'name' => 'apple',
                'count' => '10',
            ],
            [
                'name' => 'banana',
                'count' => '20',
            ],
        ], $this->result->getData());
    }
}
