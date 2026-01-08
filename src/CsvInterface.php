<?php

declare(strict_types=1);

namespace Spyck\Spreadsheet;

interface CsvInterface extends SpreadsheetInterface
{
    /**
     * Set the row delimiter.
     */
    public function setDelimiter(string $delimiter): static;

    /**
     * Set the row enclosure.
     */
    public function setEnclosure(string $enclosure): static;

    /**
     * Set the row escape.
     */
    public function setEscape(string $escape): static;

    /**
     * Set if the file is encrypted with gzip.
     */
    public function setGzip(bool $gzip): static;
}
