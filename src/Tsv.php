<?php

declare(strict_types=1);

namespace Spyck\Spreadsheet;

final class Tsv extends Csv
{
    public function __construct()
    {
        $this->setDelimiter("\t");
    }
}
