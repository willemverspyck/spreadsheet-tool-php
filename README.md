# Spreadsheet Service

Simple tool for parsing CSV or Excel spreadsheets, with the possibility to filter rows. 

## Requirements
PHP ^8.2

## Installation
Install it using [Composer](https://getcomposer.org/):

```sh
composer require spyck/spreadsheet
```

## Example usage
```php
<?php
 
declare(strict_types=1);

use Spyck\Spreadsheet\Csv;

require 'vendor/autoload.php';

/**
 * Content of test.csv.gz
 * 
 * name;count
 * apple;15
 * banana;20
 * pear;10
 * melon;4
 */

$csv = new Csv();
$csv->setGzip(true);
$csv->setFilter(function (array $data): bool {
    return $data['count'] > 10;
});

$result = $csv->getResult('test.csv.gz', [
    'name',
    'count',
]);

print_r ($result->getData());

/**
 * Result:
 * 
 * [
 *    'name' => 'apple',
 *    'count' => '15',
 * ],
 * [
 *    'name' => 'banana',
 *    'count' => '20',
 * ];
 */

$csv = new Csv();
$csv->setGzip(true);
$csv->setCallback(function (array $data): ?array {
    $data['name'] = ucfirst($data['name']);
    
    return $data;
});
$csv->setFilter(function (array $data): bool {
    return $data['count'] > 10;
});

$result = $csv->getResult('test.csv.gz', [
    'name',
    'count',
]);

print_r ($result->getData());

/**
 * Result:
 * 
 * [
 *    'name' => 'Apple',
 *    'count' => '15',
 * ],
 * [
 *    'name' => 'Banana',
 *    'count' => '20',
 * ];
 */
 
$csv = new Csv();
$csv->setGzip(true);
$csv->setEof(function (array $data, int $index): bool {
    return 'banana' === $data['name'];
});

$result = $csv->getResult('test.csv.gz', [
    'name',
    'count',
]);

print_r ($result->getData());

/**
 * Result:
 * 
 * [
 *    'name' => 'apple',
 *    'count' => '15',
 * ];
 */

print $result->getTotal();

/**
* Result:
 * 
 * 1
 */
```
