<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdaterTests\AnnotationUpdater;

use Jawira\AnnotationUpdater\Actions\Action;
use Jawira\AnnotationUpdater\Actions\Preserve;
use Jawira\AnnotationUpdater\Actions\Remove;
use Jawira\AnnotationUpdater\Actions\Replace;
use Jawira\AnnotationUpdater\AnnotationUpdater;
use Jawira\AnnotationUpdater\DocBlock\DocBlock;
use Jawira\AnnotationUpdater\DocBlock\Line;
use Jawira\AnnotationUpdaterTests\CsTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 *
 * @author Jawira Portugal <dev@tugal.be>
 * @copyright © 2026 Jawira Portugal
 */
#[CoversClass(AnnotationUpdater::class)]
#[CoversClass(Action::class)]
#[CoversClass(Preserve::class)]
#[CoversClass(Replace::class)]
#[CoversClass(Remove::class)]
#[CoversClass(DocBlock::class)]
#[CoversClass(Line::class)]
final class SkipMethodsTest extends CsTestCase
{
  public function testPreserve(): void
  {
    $code = <<<'PHP'
      <?php
      /**
       * Some description.
       *
       * @author John Connor
       */
      class Foo
      {
        /**
         * @return string
         */
        public function bar(): string {
            return 'bar';
        }
      }
      PHP;

    $expected = <<<'PHP'
      <?php
      /**
       * Some description.
       *
       * @author John Connor
       */
      class Foo
      {
        /**
         * @return string
         */
        public function bar(): string {
            return 'bar';
        }
      }
      PHP;

    $config = [
      'annotations' => [
        ['tag' => 'author', 'value' => 'John Connor', 'mode' => 'preserve'],
      ],
    ];
    $actual = $this->generateCode($code, $config);
    $this->assertSame($expected, $actual);
  }

  public function testReplace(): void
  {
    $code = <<<'PHP'
      <?php
      /**
       * Some description.
       *
       * @author John Connor
       */
      class Foo
      {
        /**
         * @author John Connor
         */
        public function bar(): string {
            return 'bar';
        }
      }
      PHP;

    $expected = <<<'PHP'
      <?php
      /**
       * Some description.
       *
       * @author Sarah Connor
       */
      class Foo
      {
        /**
         * @author John Connor
         */
        public function bar(): string {
            return 'bar';
        }
      }
      PHP;

    $config = [
      'annotations' => [
        ['tag' => 'author', 'value' => 'Sarah Connor', 'mode' => 'replace'],
      ],
    ];
    $actual = $this->generateCode($code, $config);
    $this->assertSame($expected, $actual);
  }

  public function testRemove(): void
  {
    $code = <<<'PHP'
      <?php
      /**
       * Some description.
       *
       * @author Sarah Connor
       */
      class Foo
      {
        /**
         * @author Junior Jack
         */
        public function bar(): string {
            return 'bar';
        }
      }
      PHP;

    $expected = <<<'PHP'
      <?php
      /**
       * Some description.
       *
       */
      class Foo
      {
        /**
         * @author Junior Jack
         */
        public function bar(): string {
            return 'bar';
        }
      }
      PHP;

    $config = [
      'annotations' => [
        ['tag' => 'author', 'mode' => 'remove'],
      ],
    ];
    $actual = $this->generateCode($code, $config);
    $this->assertSame($expected, $actual);
  }
}
