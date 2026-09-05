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
use PHPUnit\Framework\Attributes\DataProvider;

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
final class SkipAnnonymousClassesTest extends CsTestCase
{
  #[DataProvider('preserveProvider')]
  public function testPreserve(string $code, string $expected, array $config): void
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
         * Class with no annotation.
         */
        new class() {};
        PHP,
      <<<'PHP'
        <?php
        /**
         * Class with no annotation.
         */
        new class() {};
        PHP,
      ['annotations' => [['tag' => 'author', 'value' => 'John Connor', 'mode' => 'preserve']]],
    ];

    yield [
      <<<'PHP'
        <?php
        /**
         * Class with one annotation.
         *
         * @author Sarah Connor
         */
        new class() {};
        PHP,
      <<<'PHP'
        <?php
        /**
         * Class with one annotation.
         *
         * @author Sarah Connor
         */
        new class() {};
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
        new class() {};
        PHP,
      <<<'PHP'
        <?php
        /**
         * Class with multiple annotation.
         *
         * @author John Connor
         * @author Sarah Connor
         */
        new class() {};
        PHP,
      ['annotations' => [['tag' => 'author', 'value' => 'T-1000', 'mode' => 'preserve']]],
    ];
  }

  #[DataProvider('replaceProvider')]
  public function testReplace(string $code, string $expected, array $config): void
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
         * Class with no annotation.
         */
        new class() {};
        PHP,
      <<<'PHP'
        <?php
        /**
         * Class with no annotation.
         */
        new class() {};
        PHP,
      ['annotations' => [['tag' => 'author', 'value' => 'John Connor', 'mode' => 'replace']]],
    ];

    yield [
      <<<'PHP'
        <?php
        /**
         * Class with one annotation.
         *
         * @author Sarah Connor
         */
        new class() {};
        PHP,
      <<<'PHP'
        <?php
        /**
         * Class with one annotation.
         *
         * @author Sarah Connor
         */
        new class() {};
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
        new class() {};
        PHP,
      <<<'PHP'
        <?php
        /**
         * Class with multiple annotation.
         *
         * @author John Connor
         * @author Sarah Connor
         */
        new class() {};
        PHP,
      ['annotations' => [['tag' => 'author', 'value' => 'T-1000', 'mode' => 'replace']]],
    ];
  }

  #[DataProvider('removeProvider')]
  public function testRemove(string $code, string $expected, array $config): void
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
         * Class with no annotation.
         */
        new class() {};
        PHP,
      <<<'PHP'
        <?php
        /**
         * Class with no annotation.
         */
        new class() {};
        PHP,
      ['annotations' => [['tag' => 'author', 'mode' => 'remove']]],
    ];

    yield [
      <<<'PHP'
        <?php
        /**
         * Class with one annotation.
         *
         * @author Sarah Connor
         */
        new class() {};
        PHP,
      <<<'PHP'
        <?php
        /**
         * Class with one annotation.
         *
         * @author Sarah Connor
         */
        new class() {};
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
        new class() {};
        PHP,
      <<<'PHP'
        <?php
        /**
         * Class with multiple annotation.
         *
         * @author John Connor
         * @author Sarah Connor
         */
        new class() {};
        PHP,
      ['annotations' => [['tag' => 'author', 'mode' => 'remove']]],
    ];
  }
}
