<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdaterTests;

use Jawira\AnnotationUpdater\AnnotationUpdater;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SplFileInfo;

#[CoversClass(AnnotationUpdater::class)]
final class AnnotationUpdaterTest extends CsTestCase
{
  public function testReplacesExistingAnnotationValue(): void
  {
    $code = <<<'PHP'
      <?php
      /**
       * Some description.
       *
       * @copyright 2024 John Doe
       */
      class Foo
      {
      }
      PHP;

    $expected = <<<'PHP'
      <?php
      /**
       * Some description.
       *
       * @copyright 2026 John Doe
       */
      class Foo
      {
      }
      PHP;

    $config = [
      'annotations' => [
        ['tag' => 'copyright', 'value' => '2026 John Doe', 'mode' => 'replace'],
      ],
    ];
    $actual = $this->generateCode($code, $config);
    $this->assertSame($expected, $actual);
  }

  public function testReplaceCollapsesDuplicateTagsIntoSingleValue(): void
  {
    $code = <<<'CODE'
<?php
/**
 * Some description.
 *
 * @copyright 2024 John Doe
 * @copyright 2025 John Doe
 * @copyright 2025 John Doe
 * @copyright 2025 John Doe
 */
class Foo
{
}
CODE;

    $expected = <<<'CODE'
<?php
/**
 * Some description.
 *
 * @copyright 2026 John Doe
 */
class Foo
{
}
CODE;

    $config = [
      'annotations' => [
        ['tag' => 'copyright', 'value' => '2026 John Doe', 'mode' => 'replace'],
      ],
    ];
    $actual = $this->generateCode($code, $config);
    $this->assertSame($expected, $actual);
  }

  public function testPreserveDoesNotChangeExistingAnnotation(): void
  {
    $code = <<<'CODE'
<?php
/**
 * Some description.
 *
 * @copyright 2024 John Doe
 */
class Foo
{
}
CODE;

    $config = [
      'annotations' => [
        ['tag' => 'copyright', 'value' => '2026 John Doe', 'mode' => 'preserve'],
      ],
    ];
    $actual = $this->generateCode($code, $config);
    $this->assertSame($actual, $actual);
  }

  public function testPreserveAddsMissingAnnotation(): void
  {
    $code = <<<'CODE'
<?php
/**
 * Some description.
 */
class Foo
{
}
CODE;

    $expected = <<<'CODE'
<?php
/**
 * Some description.
 * @copyright 2026 John Doe
 */
class Foo
{
}
CODE;

    $config = [
      'annotations' => [
        ['tag' => 'copyright', 'value' => '2026 John Doe', 'mode' => 'preserve'],
      ],
    ];
    $actual = $this->generateCode($code, $config);
    $this->assertSame($expected, $actual);
  }

  public function testRemovesExistingAnnotation(): void
  {
    $code = <<<'CODE'
<?php
/**
 * Some description.
 *
 * @copyright 1991 John Connor
 * @copyright 1991 Sarah Connor
 */
class Foo
{
}
CODE;

    $expected = <<<'CODE'
<?php
/**
 * Some description.
 *
 */
class Foo
{
}
CODE;

    $config = [
      'annotations' => [
        ['tag' => 'copyright', 'mode' => 'remove'],
      ],
    ];
    $actual = $this->generateCode($code, $config);
    $this->assertSame($expected, $actual);
  }

  public function testAddsAnnotationWhenMissing(): void
  {
    $code = <<<'CODE'
<?php
/**
 * Some description.
 */
class Foo
{
}
CODE;

    $expected = <<<'CODE'
<?php
/**
 * Some description.
 * @copyright 2026 John Doe
 */
class Foo
{
}
CODE;

    $config = [
      'annotations' => [
        ['tag' => 'copyright', 'value' => '2026 John Doe', 'mode' => 'replace'],
      ],
    ];
    $actual = $this->generateCode($code, $config);
    $this->assertSame($expected, $actual);
  }

  public function testAcceptsWrappedConfiguration(): void
  {
    $config = [
      'annotations' => [
        ['tag' => 'copyright', 'value' => '2026 John Doe', 'mode' => 'replace'],
      ],
    ];
    $this->fixer->configure($config);

    self::assertSame('Jawira/annotation_updater', $this->fixer->getName());

  }

}

