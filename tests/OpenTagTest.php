<?php

namespace Jawira\AnnotationUpdaterTests;

use Jawira\AnnotationUpdaterTests\CsTestCase;

class OpenTagTest extends CsTestCase
{
  public function testWithPhpTag(): void
  {
    $code = <<<'PHP'
      <?php
      /**
       * The Bar class.
       *
       */
      class Bar
      {
      }
      PHP;

    $expected = <<<'PHP'
      <?php
      /**
       * The Bar class.
       *
       * @copyright 2026 Skynet
       */
      class Bar
      {
      }
      PHP;

    $config = ['annotations' => [['tag' => 'copyright', 'value' => '2026 Skynet', 'mode' => 'preserve']]];

    $actual = $this->generateCode($code, $config);
    $this->assertSame($expected, $actual);
  }


  /**
   * Tests require open tag `<?php` to work properly.
   *
   * The fixer has no effect when `<?php` is not present.
   */
  public function testWithoutPhpTag(): void
  {
    $code = <<<'PHP'
      /**
       * The Foo class.
       *
       * @copyright 2026 Skynet
       */
      class Foo
      {
      }
      PHP;

    $expected = <<<'PHP'
      /**
       * The Foo class.
       *
       * @copyright 2026 Skynet
       */
      class Foo
      {
      }
      PHP;

    $config = ['annotations' => [['tag' => 'copyright', 'mode' => 'remove']]];

    $actual = $this->generateCode($code, $config);
    $this->assertSame($expected, $actual);
  }
}
