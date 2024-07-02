<?php

declare(strict_types=1);

namespace Spyck\Spreadsheet;

use Closure;
use Spyck\Spreadsheet\Exception\FieldCountException;
use Spyck\Spreadsheet\Exception\FieldRequiredException;

abstract class AbstractSpreadsheet implements SpreadsheetInterface
{
    protected ?int $header = 1;
    protected ?Closure $callback = null;
    protected ?Closure $eof = null;
    protected ?Closure $filter = null;
    protected bool $check = true;

    public static function create(): static
    {
        return new static();
    }

    /**
     * {@inheritDoc}
     */
    public function getResult(string $file, array $fields): Result
    {
        $this->openResource($file);

        $returnData = [];

        $rowCount = 0;
        $rowFields = null;
        $rowHeader = $this->header;
        $rowContent = 0;

        $hasHeader = null !== $rowHeader;

        $persist = true;

        while (null !== ($row = $this->handleRow())) {
            $row = array_map(function (?string $data): ?string {
                if (null === $data) {
                    return null;
                }

                return trim($data);
            }, $row);

            ++$rowCount;

            if ($hasHeader) {
                if ($rowCount === $rowHeader) {
                    $rowDifference = array_diff(array_keys($fields), $row);

                    if (0 !== count($rowDifference)) {
                        throw new FieldRequiredException(sprintf(
                            'Missing required fields (%s) (%s)',
                            implode(', ', $rowDifference),
                            implode(', ', $row),
                        ));
                    }

                    $rowFields = $row;
                    $rowContent = $rowHeader + 1;
                }
            } else {
                if (null === $rowFields) {
                    $rowFields = array_keys($row);
                    $rowContent = 1;
                }
            }

            if (null !== $rowFields) {
                if ($rowCount >= $rowContent) {
                    if (null !== $this->eof) {
                        $eof = call_user_func($this->eof, $row, $rowCount - $rowContent);

                        if ($eof) {
                            $persist = false;
                        }
                    }

                    if ($persist) {
                        $countFields = count($rowFields);
                        $countRow = count($row);

                        if ($countFields > $countRow) {
                            if ($this->check) {
                                throw new FieldCountException(sprintf(
                                    'Incorrect field count on line %d (%d instead of %d)',
                                    $rowCount,
                                    $countFields,
                                    $countRow,
                                ));
                            } else {
                                $row = array_pad($row, $countFields, null);
                            }
                        } elseif ($countFields < $countRow) {
                            $row = array_slice($row, 0, $countFields);
                        }

                        $returnRow = $this->handleTranslate($fields, $rowFields, $row);

                        if (null !== $this->filter) {
                            $filter = call_user_func($this->filter, $returnRow);

                            if ($filter) {
                                $returnData[] = $this->handleCallback($returnRow, $rowCount);
                            }
                        } else {
                            $returnData[] = $this->handleCallback($returnRow, $rowCount);
                        }
                    }
                }
            }
        }

        $result = new Result();
        $result->setCount(count($returnData));
        $result->setTotal($rowCount);
        $result->setData($returnData);

        $this->closeResource();

        return $result;
    }

    public function setCallback(callable $callback): self
    {
        $this->callback = $callback;

        return $this;
    }

    public function setFilter(callable $filter): self
    {
        $this->filter = $filter;

        return $this;
    }

    public function setEof(callable $eof): self
    {
        $this->eof = $eof;

        return $this;
    }

    public function setHeader(?int $header): self
    {
        $this->header = $header;

        return $this;
    }

    public function setCheck(bool $check): self
    {
        $this->check = $check;

        return $this;
    }

    /**
     * Handle the callback
     */
    protected function handleCallback(array $data, int $index): ?array
    {
        if (null === $this->callback) {
            return $data;
        }

        return call_user_func($this->callback, $data, $index);
    }

    protected function handleRow(): ?array
    {
        return null;
    }

    /**
     * Translate CSV data
     */
    protected function handleTranslate(array $fields, array $rowFields, array $row): array
    {
        $data = [];

        foreach (array_combine($rowFields, $row) as $fieldName => $fieldValue) {
            if (array_key_exists($fieldName, $fields) && null !== $fields[$fieldName]) {
                $data[$fields[$fieldName]] = $fieldValue;
            }
        }

        return $data;
    }

    protected function openResource(string $file): void
    {
    }

    protected function closeResource(): void
    {
    }
}
