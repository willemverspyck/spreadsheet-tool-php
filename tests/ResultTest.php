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
        $this->result->setTotal(10);
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
        self::assertSame(2, $this->result->getCount());
    }

    public function testGetTotal(): void
    {
        self::assertSame(10, $this->result->getTotal());
    }

    public function testGetData(): void
    {
        self::assertSame([
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
