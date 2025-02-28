# RelativeDateParser

RelativeDateParser is a PHP library that parses relative date expressions in natural language and converts them to actual dates. It currently supports German language date expressions like "Zweiter Dienstag des Monats" (Second Tuesday of the month), "Alle 14 Tage" (Every 14 days), or "Jeder 5. Tag eines Monats" (Every 5th day of a month).

## Origin

This library was originally created by Tim Schumacher and has been updated to work with Symfony 7.2 and PHP 8.2.

## Installation

You can install the library via Composer:

```bash
composer require enko/relativedateparser
```

## Requirements

- PHP 8.2 or higher
- Symfony 7.2 components (Translation, Config)

## Usage

### Basic Usage

```php
use enko\RelativeDateParser\RelativeDateParser;

// Create a parser for a specific date expression
// Parameters: expression, reference date (optional), language (default: 'en')
$parser = new RelativeDateParser('Zweiter Dienstag des Monats', new \DateTime(), 'de');

// Get the current date matching the expression
$currentDate = $parser->getCurrent();

// Get the next date matching the expression
$nextDate = $parser->getNext();
```

### Examples of Supported Expressions

1. **Nth weekday of the month**
   ```php
   $parser = new RelativeDateParser('Zweiter Dienstag des Monats', new \DateTime(), 'de');
   echo $parser->getNext()->format('Y-m-d'); // e.g., 2023-11-14
   ```

2. **Every X days/weeks**
   ```php
   $parser = new RelativeDateParser('Alle 14 Tage', new \DateTime(), 'de');
   echo $parser->getNext()->format('Y-m-d'); // Date 14 days from now
   ```

3. **Every Nth day of a month**
   ```php
   $parser = new RelativeDateParser('Jeder 5. Tag eines Monats', new \DateTime(), 'de');
   echo $parser->getNext()->format('Y-m-d'); // Next 5th day of a month
   ```

### Custom Reference Date

You can specify a custom reference date for the calculations:

```php
$referenceDate = new \DateTime('2023-10-01');
$parser = new RelativeDateParser('Erster Montag des Monats', $referenceDate, 'de');
echo $parser->getCurrent()->format('Y-m-d'); // 2023-10-02
echo $parser->getNext()->format('Y-m-d'); // 2023-11-06
```

## Development

### Running Tests

The library uses PHPUnit for testing. To run the tests:

```bash
composer install
vendor/bin/phpunit tests/
```

### Adding New Date Expressions

To add support for new date expression types:

1. Create a new class that extends `RelativeDateType`
2. Implement the required methods `getNext()` and `getCurrent()`
3. Add the pattern matching in the `RelativeDateParser` class

### Translation Support

The library supports translations through Symfony's Translation component. Translation files should be placed in the `translations` directory as .po files.
