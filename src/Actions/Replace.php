<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdater\Actions;

use Jawira\AnnotationUpdater\DocBlock\DocBlock;
use function array_splice;
use function in_array;

/**
 * Replace annotation.
 */
final class Replace extends Action
{
  public const MODE = 'replace';

  public function __construct(string $tag, string $value, string $mode)
  {
    $this->tag = $tag;
    $this->value = $value;
    $mode === self::MODE or throw new \InvalidArgumentException("Invalid mode '$mode'");
  }

  public function apply(DocBlock $docBlock): DocBlock
  {
    while ($docBlock->countTheTag($this->tag) > 1) {
      $docBlock->removeLastTag($this->tag);
    }

    // Tag was never present
    if ($docBlock->countTheTag($this->tag) === 0) {
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
