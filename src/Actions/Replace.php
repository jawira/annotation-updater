<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdater\Actions;

use InvalidArgumentException;
use Jawira\AnnotationUpdater\DocBlock\DocBlock;
use Override;

use function is_int;

/**
 * Replace annotation.
 *
 * @author Jawira Portugal <dev@tugal.be>
 * @copyright © 2026 Jawira Portugal
 */
final class Replace extends Action
{
  public function __construct(string $tag, string $value, string $mode)
  {
    $this->tag = $tag;
    $this->value = $value;
    Replace::getMode() === $mode or throw new InvalidArgumentException("Invalid mode '{$mode}'");
  }

  #[Override]
  public static function getMode(): string
  {
    return 'replace';
  }

  #[Override]
  public function apply(DocBlock $docBlock): DocBlock
  {
    // Leave one tag if there's many
    while ($docBlock->countTheTag($this->tag) > 1) {
      $docBlock->removeLastTag($this->tag);
    }

    $index = $docBlock->lastPositionForTheTag($this->tag);
    // tag was never present
    if (!is_int($index)) {
      $docBlock->removeTrailingBlankLines();
      $docBlock->pushLine($this->forgeLines());

      return $docBlock;
    }

    // Replace the last tag remaining
    $docBlock->removeLastTag($this->tag);
    $docBlock->insertLines($this->forgeLines(), $index);

    return $docBlock;
  }
}
