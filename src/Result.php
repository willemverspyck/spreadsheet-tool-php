<?php

declare(strict_types=1);

namespace Spyck\Spreadsheet;

final class Result
{
    private int $count;

    private int $countRow;

    private array $data;

    /**
     * Get number of matches
     */
    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get number of rows
     */
    public function getCountRow(): int
    {
        return $this->countRow;
    }

    public function setCountRow(int $countRow): self
    {
        $this->countRow = $countRow;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }
}
