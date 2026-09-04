![jawira/annotation-updater](./resources/jawira-annotation-updater.svg)

[![PHP Version Require](https://poser.pugx.org/jawira/annotation-updater/require/php?style=for-the-badge)](https://packagist.org/packages/jawira/annotation-updater)
[![Latest Stable Version](https://poser.pugx.org/jawira/annotation-updater/v?style=for-the-badge)](https://packagist.org/packages/jawira/annotation-updater)
[![License](https://poser.pugx.org/jawira/annotation-updater/license?style=for-the-badge)](https://packagist.org/packages/jawira/annotation-updater)

This [PHP-CS-Fixer](https://cs.symfony.com/) rule allows you to **manage PHPDoc
tags** in classes, interfaces, traits, and enums.   
It helps maintain consistent documentation across your codebase.

## Installation

```shell
composer require --dev jawira/annotation-updater
```

## Usage

### Registering the fixer

In your `.php-cs-fixer.php` register and configure `AnnotationUpdater`:

1. Use `Config::registerCustomFixers()` to register an instance of
   `AnnotationUpdater`.
2. Use `Config::setRules()` to specify fixer's configuration under the
   `Jawira/annotation_updater` key.

Example:

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
        ['tag' => 'author', 'value' => 'John Connor', 'mode' => 'preserve'],
        ['tag' => 'license', 'value' => 'MIT', 'mode' => 'replace'],
        ['tag' => 'todo', 'mode' => 'remove'],
      ],
    ],
  ]);
```

This is the result after executing the previous configuration:

```diff
/**
 * @author Sarah Connor
- * @license proprietary
- * @todo add type hints
+ * @license MIT
 */
class Example{
}
```

### Configuration

The fixer accepts a single configuration key: `annotations`. This is an array of
annotation rules to apply.

Each rule is an array with the following structure:

- `tag`: The PHPDoc tag name without the `@`.
- `value`: The value of the tad. Do not set this key for `remove` mode!
- `mode`: One of `preserve`, `replace`, or `remove`.

Example:

```php
[
  'tag' => 'author',
  'value' => 'John Connor',
  'mode' => 'preserve',
]
```

### Mode

The fixer has three different modes.

1. `preserve`: Do not replace the tasg if it's already present.
2. `replace`: Always replace the tag.
3. `remove`: Remove the tag.

The `mode` behavior is summarized in the following table.

| Mode       | Tag is not present | Tag is present |
|------------|--------------------|----------------|
| `preserve` | Add tag            | Do nothing     |
| `replace`  | Add tag            | Replace tag    |
| `remove`   | Do nothing         | Remove tag     |

## Contributing

- If you liked this project, ⭐ star it on GitHub.
- Or follow me on 𝕏.
  [![𝕏 Follow](https://img.shields.io/twitter/follow/jawira?style=social)](https://x.com/jawira)
- Found a bug? Please report it by opening an issue!

## License

This library is licensed under the [MIT license](LICENSE.md).

## Disclaimer

* Yes, I know `Annotations` are not the same as `PHPDoc tags`, but when I
  realized this it was too late. Properly speaking, this library is supposed to
  update `PHPDoc tags` and not `Annotations`.
* This project is inspired by `konradmichalik/php-doc-block-header-fixer`.

***

## Packages from jawira

<dl>

<dt>
  <a href="https://packagist.org/packages/jawira/doctrine-diagram-bundle">jawira/doctrine-diagram-bundle
  <img alt="GitHub stars" src="https://badgen.net/github/stars/jawira/doctrine-diagram-bundle?icon=github"/></a>
</dt>
<dd>Symfony Bundle to generate database diagrams.</dd>

<dt>
  <a href="https://packagist.org/packages/jawira/case-converter">jawira/case-converter
  <img alt="GitHub stars" src="https://badgen.net/github/stars/jawira/case-converter?icon=github"/></a>
</dt>
<dd>Convert strings between 13 naming conventions: Snake case, Camel case,
  Pascal case, Kebab case, Ada case, Train case, Cobol case, Macro case,
  Upper case, Lower case, Sentence case, Title case and Dot notation.
</dd>

<dt><a href="https://packagist.org/packages/jawira/">more...</a></dt>
</dl>
