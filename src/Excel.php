<?php

declare(strict_types=1);

namespace Spyck\Spreadsheet;

use InvalidArgumentException;
use LogicException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Spyck\Spreadsheet\Exception\NotFoundException;

final class Excel extends AbstractSpreadsheet
{
    private array $file;

    public function __construct()
    {
        if (false === class_exists(Xlsx::class)) {
            throw new LogicException('You need to add "phpoffice/phpspreadsheet" as a Composer dependency.');
        }
    }

    /**
     * @throws NotFoundException
     */
    protected function openResource(string $file): void
    {
        $xlsx = new Xlsx();
        $xlsx->setReadDataOnly(true);

        try {
            $spreadsheet = $xlsx->load($file);
        } catch (InvalidArgumentException $exception) {
            throw new NotFoundException($exception->getMessage());
        }

        $worksheet = $spreadsheet->getActiveSheet();

        $this->file = $worksheet->toArray();
    }

    protected function handleRow(): ?array
    {
        return array_shift($this->file);
    }
}
