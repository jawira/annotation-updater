<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdaterTests;

use Jawira\AnnotationUpdater\AnnotationUpdater;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 *
 * @author Jawira Portugal <dev@tugal.be>
 * @copyright © 2026 Jawira Portugal
 */
#[CoversClass(AnnotationUpdater::class)]
final class CreateDocblockTest extends CsTestCase
{
  public function testPreserve(): void
  {
    $code = <<<'PHP'
      <?php
      class Foo
      {
      }
      PHP;

    $expected = <<<'PHP'
      <?php
      /**
       * @author John Connor
       */
      class Foo
      {
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
      class Foo
      {
      }
      PHP;

    $expected = <<<'PHP'
      <?php
      /**
       * @author Sarah Connor
       */
      class Foo
      {
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
      class Foo
      {
      }
      PHP;

    $expected = <<<'PHP'
      <?php
      class Foo
      {
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
