<?php

declare(strict_types=1);

namespace Spyck\Spreadsheet;

use Spyck\Spreadsheet\Exception\FieldCountException;
use Spyck\Spreadsheet\Exception\FieldRequiredException;
use Spyck\Spreadsheet\Exception\NotFoundException;

interface SpreadsheetInterface
{
    /**
     * Get CSV data from file.
     *
     * @throws FieldCountException
     * @throws FieldRequiredException
     * @throws NotFoundException
     */
    public function getResult(string $file, array $fields): Result;

    public function setCallback(callable $callback): static;

    public function setFilter(callable $filter): static;

    public function setHeader(?int $header): static;

    public function setEof(callable $eof): static;

    public function setCheck(bool $check): static;
}
