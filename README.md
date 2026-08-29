# Annotation fixer

Custom _PHP-CS-Fixer_ fixer for updating PHPDoc annotation.

This project is inspired by header-style DocBlock fixers, but it focuses on a
smaller, more explicit use case: updating specific PHPDoc tags such as
`@copyright`, `@author`, `@license`, and similar annotations without changing
the surrounding description text.

## Installation

```shell
composer require --dev jawira/annotation-updater
```

## Registering the fixer

In your `.php-cs-fixer.php` file:

```php
<?php
use Jawira\AnnotationUpdater\AnnotationUpdater;
use PhpCsFixer\Config;

return (new Config())
  ->registerCustomFixers([
    new AnnotationUpdater(),
  ])
  ->setRules([
    'Jawira/annotation_updater' => [
      'annotations' => [
        [ 'tag' => 'license', 'value' => 'MIT', 'mode' => 'preserve'],
        [ 'tag' => 'copyright', 'value' => '© 2026 John Connor', 'mode' => 'replace'],
        [ 'tag' => 'todo', 'mode' => 'remove'],
      ],
    ],
  ]);
```

## Configuration

The fixer accepts an `annotations` list, where each item has:

- `tag`: The PHPDoc tag name without the `@`, for example `copyright`
- `value`: The value to set or append. Do not use for `remove` mode.
- `mode`: One of `replace`, `append`, or `remove`

### Modes

Tag aka annotations

| Mode       | Missing tag | Existing tag | Multiple tags        |
|------------|-------------|--------------|----------------------|
| `preserve` | Add tag     | Do nothing   | Do nothing           |
| `replace`  | Add tag     | Replace tag  | Replace with one tag |
| `remove`   | Do nothing  | Remove tag   | Remove tags          |
