<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdater\Actions;

use InvalidArgumentException;
use Jawira\AnnotationUpdater\DocBlock\DocBlock;

/**
 * Replace annotation.
 *
 * @author Jawira Portugal <dev@tugal.be>
 * @copyright © 2026 Jawira Portugal
 */
final class Replace extends Action
{
  public const MODE = 'replace';

  public function __construct(string $tag, string $value, string $mode)
  {
    $this->tag = $tag;
    $this->value = $value;
    self::MODE === $mode or throw new InvalidArgumentException("Invalid mode '{$mode}'");
  }

  public function apply(DocBlock $docBlock): DocBlock
  {
    while ($docBlock->countTheTag($this->tag) > 1) {
      $docBlock->removeLastTag($this->tag);
    }

    // Tag was never present
    if (0 === $docBlock->countTheTag($this->tag)) {
      $docBlock->removeTrailingBlankLines();
      $docBlock->pushLine($this->forgeLines());

      return $docBlock;
    }

    // Replace the last tag remaining
    $position = $docBlock->lastPositionForTheTag($this->tag);
    $docBlock->removeLastTag($this->tag);
    $docBlock->insertLines($this->forgeLines(), $position);

    return $docBlock;
  }
}
