<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdaterTests\AnnotationUpdater;

use Jawira\AnnotationUpdater\AnnotationUpdater;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(AnnotationUpdater::class)]
final class MultipleUsageTest extends \Jawira\AnnotationUpdaterTests\CsTestCase
{
  #[DataProvider('annotationProvider')]
  public function testMultipleAnnotations($code, $expected, $config): void
  {
    $actual = $this->generateCode($code, $config);
    $this->assertSame($expected, $actual);
  }

  public static function annotationProvider(): iterable
  {
    yield [
      <<<'PHP'
      <?php
      class Foo
      {
      }
      PHP,
      <<<'PHP'
      <?php
      /**
       * @author Dr. Miles Dyson <md@skynet.com>
       * @copyright 2026 Skynet
       * @license MIT
       */
      class Foo
      {
      }
      PHP,
      ['annotations' => [
        ['tag' => 'author', 'value' => 'Dr. Miles Dyson <md@skynet.com>', 'mode' => 'preserve'],
        ['tag' => 'copyright', 'value' => '2026 Skynet', 'mode' => 'replace'],
        ['tag' => 'license', 'value' => 'MIT', 'mode' => 'replace'],
        ['tag' => 'todo',  'mode' => 'remove'],
      ]],
    ];

    yield [
      <<<'PHP'
      <?php

      class Hana {
        public $foo;
      }
      PHP,
      <<<'PHP'
      <?php

      /**
       * @author Jawira Portugal
       * @copyright © 2025-2026 Jawira Portugal
       * @license MIT
       */
      class Hana {
        public $foo;
      }
      PHP,
      [
        'annotations' => [
          ['tag' => 'author', 'value' => 'Jawira Portugal', 'mode' => 'preserve'],
          ['tag' => 'copyright', 'value' => "© 2025-2026 Jawira Portugal", 'mode' => 'replace'],
          ['tag' => 'license', 'value' => 'MIT', 'mode' => 'preserve'],
        ],
      ],
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

}
