<?php declare(strict_types=1);


use Jawira\AnnotationUpdater\AnnotationUpdater;
use Jawira\AnnotationUpdaterTests\CsTestCase;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;


#[CoversClass(AnnotationUpdater::class)]
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

