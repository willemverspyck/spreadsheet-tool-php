<?php

declare(strict_types=1);

namespace Spyck\Spreadsheet;

interface CsvInterface extends SpreadsheetInterface
{
    /**
     * Set the row delimiter
     */
    public function setDelimiter(string $delimiter): self;

    /**
     * Set the row enclosure
     */
    public function setEnclosure(string $enclosure): self;

    /**
     * Set the row escape
     */
    public function setEscape(string $escape): self;

    /**
     * Set if the file is encrypted with gzip
     */
    public function setGzip(bool $gzip): self;
}
