<?php declare(strict_types=1);


use Jawira\AnnotationUpdater\AnnotationUpdater;
use Jawira\AnnotationUpdaterTests\CsTestCase;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass(AnnotationUpdater::class)]
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

