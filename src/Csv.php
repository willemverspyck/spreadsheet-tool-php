<?php

declare(strict_types=1);

namespace Spyck\Spreadsheet;

use LogicException;
use RuntimeException;
use SplFileObject;
use Spyck\Spreadsheet\Exception\NotFoundException;

class Csv extends AbstractSpreadsheet implements CsvInterface
{
    private string $delimiter = ',';

    private string $enclosure = '"';

    private string $escape = '\\';

    private bool $gzip = false;

    private ?SplFileObject $splFileObject = null;

    /**
     * {@inheritDoc}
     */
    public function setDelimiter(string $delimiter): self
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setEnclosure(string $enclosure): self
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setEscape(string $escape): self
    {
        $this->escape = $escape;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setGzip(bool $gzip): self
    {
        $this->gzip = $gzip;

        return $this;
    }

    protected function handleRow(): ?array
    {
        if ($this->splFileObject->eof()) {
            return null;
        }

        $line = $this->splFileObject->fgets();

        /** Skip last empty line, SplFileObject::DROP_NEW_LINE not working as expected */
        if (strlen($line) === 0 && $this->splFileObject->eof()) {
            return null;
        }

        return str_getcsv($line, $this->delimiter, $this->enclosure, $this->escape);
    }

    /**
     * @throws LogicException
     * @throws NotFoundException
     * @throws RuntimeException
     */
    protected function openResource(string $file): void
    {
        if ($this->gzip) {
            $file = sprintf('compress.zlib://%s', $file);
        }

        try {
            $this->splFileObject = new SplFileObject($file, 'r');
        } catch (LogicException|RuntimeException $exception) {
            throw new NotFoundException($exception->getMessage());
        }
    }

    protected function closeResource(): void
    {
        $this->splFileObject = null;
    }
}
