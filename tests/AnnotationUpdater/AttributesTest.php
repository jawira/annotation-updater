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
class AttributesTest extends CsTestCase
{
  private const FOO = <<<'PHP'
    <?php
    // test
    #[\FooAttribute]
    readonly class Foo {
    }
    PHP;

  private const BAR = <<<'PHP'
    <?php
    /**
     * The Bar class.
     *
     * @author Jawira Portugal
     */
    #[\MyBarAttribute]
    #[\AnyBarAttribute]
    abstract class Bar {
        private $bar;
    }
    PHP;

  private const BAZ = <<<'PHP'
    <?php
    #[\BazAttribute]
    #[\AnotherBazAttribute]
    /**
     * @license MIT
     * @copyright (c) Copyright 2026
     */
    readonly final class Baz {
        protected string $baz;
    }
    PHP;

  #[DataProvider('preserveProvider')]
  public function testPreserve(string $code, string $expected, array $config): void
  {
    $actual = $this->generateCode($code, $config);
    $this->assertSame($expected, $actual);
  }

  public static function preserveProvider(): iterable
  {
    yield [
      self::FOO,
      self::FOO,
      ['annotations' => []],
    ];

    yield [
      self::FOO,
      <<<'PHP'
        <?php
        // test
        /**
         * @demo This is a demo
         */
        #[\FooAttribute]
        readonly class Foo {
        }
        PHP,
      ['annotations' => [
        ['tag' => 'demo', 'value' => 'This is a demo', 'mode' => 'preserve'],
      ]],
    ];

    yield [
      self::FOO,
      <<<'PHP'
        <?php
        // test
        /**
         * @demo This is a demo
         * @license MIT
         */
        #[\FooAttribute]
        readonly class Foo {
        }
        PHP,
      ['annotations' => [
        ['tag' => 'demo', 'value' => 'This is a demo', 'mode' => 'preserve'],
        ['tag' => 'license', 'value' => 'MIT', 'mode' => 'preserve'],
      ]],
    ];

    yield [
      self::BAR,
      <<<'PHP'
        <?php
        /**
         * The Bar class.
         *
         * @author Jawira Portugal
         */
        #[\MyBarAttribute]
        #[\AnyBarAttribute]
        abstract class Bar {
            private $bar;
        }
        PHP,
      ['annotations' => [
        ['tag' => 'author', 'value' => 'Junior Jack', 'mode' => 'preserve'],
      ]],
    ];

    yield [
      self::BAZ,
      <<<'PHP'
        <?php
        #[\BazAttribute]
        #[\AnotherBazAttribute]
        /**
         * @license MIT
         * @copyright (c) Copyright 2026
         */
        readonly final class Baz {
            protected string $baz;
        }
        PHP,
      ['annotations' => [
        ['tag' => 'license', 'value' => 'proprietary', 'mode' => 'preserve'],
      ]],
    ];

    yield [
      self::BAZ,
      <<<'PHP'
        <?php
        #[\BazAttribute]
        #[\AnotherBazAttribute]
        /**
         * @license MIT
         * @copyright (c) Copyright 2026
         * @author Jawira
         */
        readonly final class Baz {
            protected string $baz;
        }
        PHP,
      ['annotations' => [
        ['tag' => 'license', 'value' => 'proprietary', 'mode' => 'preserve'],
        ['tag' => 'author', 'value' => 'Jawira', 'mode' => 'preserve'],
      ]],
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
      self::FOO,
      <<<'PHP'
        <?php
        // test
        /**
         * @demo This is a demo
         */
        #[\FooAttribute]
        readonly class Foo {
        }
        PHP,
      ['annotations' => [
        ['tag' => 'demo', 'value' => 'This is a demo', 'mode' => 'replace'],
      ]],
    ];

    yield [
      self::FOO,
      <<<'PHP'
        <?php
        // test
        /**
         * @demo This is a demo
         * @license MIT
         */
        #[\FooAttribute]
        readonly class Foo {
        }
        PHP,
      ['annotations' => [
        ['tag' => 'demo', 'value' => 'This is a demo', 'mode' => 'replace'],
        ['tag' => 'license', 'value' => 'MIT', 'mode' => 'replace'],
      ]],
    ];

    yield [
      self::BAR,
      <<<'PHP'
        <?php
        /**
         * The Bar class.
         *
         * @author Junior Jack
         */
        #[\MyBarAttribute]
        #[\AnyBarAttribute]
        abstract class Bar {
            private $bar;
        }
        PHP,
      ['annotations' => [
        ['tag' => 'author', 'value' => 'Junior Jack', 'mode' => 'replace'],
      ]],
    ];

    yield [
      self::BAZ,
      <<<'PHP'
        <?php
        #[\BazAttribute]
        #[\AnotherBazAttribute]
        /**
         * @license proprietary
         * @copyright (c) Copyright 2026
         */
        readonly final class Baz {
            protected string $baz;
        }
        PHP,
      ['annotations' => [
        ['tag' => 'license', 'value' => 'proprietary', 'mode' => 'replace'],
      ]],
    ];

    yield [
      self::BAZ,
      <<<'PHP'
        <?php
        #[\BazAttribute]
        #[\AnotherBazAttribute]
        /**
         * @license proprietary
         * @copyright (c) Copyright 2026
         * @author Jawira
         */
        readonly final class Baz {
            protected string $baz;
        }
        PHP,
      ['annotations' => [
        ['tag' => 'license', 'value' => 'proprietary', 'mode' => 'replace'],
        ['tag' => 'author', 'value' => 'Jawira', 'mode' => 'replace'],
      ]],
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
      self::FOO,
      <<<'PHP'
        <?php
        // test
        #[\FooAttribute]
        readonly class Foo {
        }
        PHP,
      ['annotations' => [
        ['tag' => 'demo', 'mode' => 'remove'],
      ]],
    ];

    yield [
      self::FOO,
      <<<'PHP'
        <?php
        // test
        #[\FooAttribute]
        readonly class Foo {
        }
        PHP,
      ['annotations' => [
        ['tag' => 'demo', 'mode' => 'remove'],
        ['tag' => 'license', 'mode' => 'remove'],
      ]],
    ];

    yield [
      self::BAR,
      <<<'PHP'
        <?php
        /**
         * The Bar class.
         *
         */
        #[\MyBarAttribute]
        #[\AnyBarAttribute]
        abstract class Bar {
            private $bar;
        }
        PHP,
      ['annotations' => [
        ['tag' => 'author', 'mode' => 'remove'],
      ]],
    ];

    yield [
      self::BAZ,
      <<<'PHP'
        <?php
        #[\BazAttribute]
        #[\AnotherBazAttribute]
        /**
         * @copyright (c) Copyright 2026
         */
        readonly final class Baz {
            protected string $baz;
        }
        PHP,
      ['annotations' => [
        ['tag' => 'license', 'mode' => 'remove'],
      ]],
    ];

    yield [
      self::BAZ,
      <<<'PHP'
        <?php
        #[\BazAttribute]
        #[\AnotherBazAttribute]
        /**
         * @copyright (c) Copyright 2026
         */
        readonly final class Baz {
            protected string $baz;
        }
        PHP,
      ['annotations' => [
        ['tag' => 'license', 'mode' => 'remove'],
        ['tag' => 'author', 'mode' => 'remove'],
      ]],
    ];
  }
}
