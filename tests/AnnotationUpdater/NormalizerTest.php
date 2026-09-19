<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdaterTests\AnnotationUpdater;

use Jawira\AnnotationUpdater\AnnotationUpdater;
use Jawira\AnnotationUpdaterTests\CsTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 *
 * @author Jawira Portugal <dev@tugal.be>
 * @copyright © 2026 Jawira Portugal
 */
#[CoversClass(AnnotationUpdater::class)]
class NormalizerTest extends CsTestCase
{
  public function testNoTag(): void
  {
    $this->expectExceptionMessageMatches('#has an invalid tag#');
    $config = ['annotations' => [['value' => 'test', 'mode' => 'preserve']]];
    $this->applyAnnotationUpdater('', $config);
  }

  public function testEmptyTag(): void
  {
    $this->expectExceptionMessageMatches('#has an empty tag#');
    $config = ['annotations' => [['tag' => '', 'value' => 'test', 'mode' => 'preserve']]];
    $this->applyAnnotationUpdater('', $config);
  }

  public function testInvalidMode(): void
  {
    $this->expectExceptionMessageMatches('#has an invalid mode#');
    $config = ['annotations' => [['tag' => 'author', 'value' => 'test', 'mode' => 'dummy']]];
    $this->applyAnnotationUpdater('', $config);
  }
}
