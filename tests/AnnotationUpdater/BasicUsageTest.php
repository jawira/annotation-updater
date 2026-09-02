<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdaterTests\AnnotationUpdater;

use Jawira\AnnotationUpdater\AnnotationUpdater;
use Jawira\AnnotationUpdaterTests\CsTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(AnnotationUpdater::class)]
final class BasicUsageTest extends CsTestCase
{
  #[DataProvider('preserveProvider')]
  public function testPreserve($code, $expected, $config): void
  {
    $actual = $this->generateCode($code, $config);
    $this->assertSame($expected, $actual);
  }

  public static function preserveProvider(): iterable
  {
    yield [
      <<<'PHP'
      <?php
      /**
       * Some description.
       */
      class Foo
      {
      }
      PHP,
      <<<'PHP'
      <?php
      /**
       * Some description.
       * @copyright 2026 Skynet
       */
      class Foo
      {
      }
      PHP,
      ['annotations' => [['tag' => 'copyright', 'value' => '2026 Skynet', 'mode' => 'preserve']]],
    ];

    yield [
      <<<'PHP'
      <?php
      /**
       * Some description.
       *
       */
      class Foo
      {
      }
      PHP,
      <<<'PHP'
      <?php
      /**
       * Some description.
       * @copyright 2026 Skynet
       */
      class Foo
      {
      }
      PHP,
      ['annotations' => [['tag' => 'copyright', 'value' => '2026 Skynet', 'mode' => 'preserve']]],
    ];

    yield [
      <<<'PHP'
      <?php
      /**
       * Class with one annotation.
       *
       * @author Sarah Connor
       */
      class testing {
      }
      PHP,
      <<<'PHP'
      <?php
      /**
       * Class with one annotation.
       *
       * @author Sarah Connor
       */
      class testing {
      }
      PHP,
      ['annotations' => [['tag' => 'author', 'value' => 'John Connor', 'mode' => 'preserve']]],
    ];

    yield [
      <<<'PHP'
      <?php
      /**
       * Class with multiple annotation.
       *
       * @author John Connor
       * @author Sarah Connor
       */
      class Foo {};
      PHP,
      <<<'PHP'
      <?php
      /**
       * Class with multiple annotation.
       *
       * @author John Connor
       * @author Sarah Connor
       */
      class Foo {};
      PHP,
      ['annotations' => [['tag' => 'author', 'value' => 'T-1000', 'mode' => 'preserve']]],
    ];
  }

  #[DataProvider('replaceProvider')]
  public function testReplace($code, $expected, $config): void
  {
    $actual = $this->generateCode($code, $config);
    $this->assertSame($expected, $actual);
  }

  public static function replaceProvider(): iterable
  {
    yield [
      <<<'PHP'
      <?php
      /**
       * Some description.
       *
       */
      class Foo
      {
      }
      PHP,
      <<<'PHP'
      <?php
      /**
       * Some description.
       *
       * @copyright 2026 Skynet
       */
      class Foo
      {
      }
      PHP,
      ['annotations' => [['tag' => 'copyright', 'value' => '2026 Skynet', 'mode' => 'replace']]],
    ];

    yield [
      <<<'PHP'
      <?php
      /**
       * Class with one annotation.
       *
       * @author Sarah Connor
       */
      class testing {
      }
      PHP,
      <<<'PHP'
      <?php
      /**
       * Class with one annotation.
       *
       * @author John Connor
       */
      class testing {
      }
      PHP,
      ['annotations' => [['tag' => 'author', 'value' => 'John Connor', 'mode' => 'replace']]],
    ];

    yield [
      <<<'PHP'
      <?php
      /**
       * Class with multiple annotation.
       *
       * @author John Connor
       * @author Sarah Connor
       */
      class Foo {};
      PHP,
      <<<'PHP'
      <?php
      /**
       * Class with multiple annotation.
       *
       * @author T-1000
       */
      class Foo {};
      PHP,
      ['annotations' => [['tag' => 'author', 'value' => 'T-1000', 'mode' => 'replace']]],
    ];
  }


  #[DataProvider('removeProvider')]
  public function testRemove($code, $expected, $config): void
  {
    $actual = $this->generateCode($code, $config);
    $this->assertSame($expected, $actual);
  }

  public static function removeProvider(): iterable
  {
    yield [
      <<<'PHP'
      <?php
      /**
       * Some description.
       *
       */
      class Foo
      {
      }
      PHP,
      <<<'PHP'
      <?php
      /**
       * Some description.
       *
       */
      class Foo
      {
      }
      PHP,
      ['annotations' => [['tag' => 'copyright', 'mode' => 'remove']]],
    ];

    yield [
      <<<'PHP'
      <?php
      /**
       * Class with one annotation.
       *
       * @author Sarah Connor
       */
      class testing {
      }
      PHP,
      <<<'PHP'
      <?php
      /**
       * Class with one annotation.
       *
       */
      class testing {
      }
      PHP,
      ['annotations' => [['tag' => 'author', 'mode' => 'remove']]],
    ];

    yield [
      <<<'PHP'
      <?php
      /**
       * Class with multiple annotation.
       *
       * @author John Connor
       * @author Sarah Connor
       */
      class Foo {}
      PHP,
      <<<'PHP'
      <?php
      /**
       * Class with multiple annotation.
       *
       */
      class Foo {}
      PHP,
      ['annotations' => [['tag' => 'author', 'mode' => 'remove']]],
    ];
  }
}
