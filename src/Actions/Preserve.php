<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdater\Actions;

use InvalidArgumentException;
use Jawira\AnnotationUpdater\DocBlock\DocBlock;

/**
 * Do not modify annotation if it's already present.
 *
 * @author Jawira Portugal <dev@tugal.be>
 * @copyright © 2026 Jawira Portugal
 */
final class Preserve extends Action
{
  public const MODE = 'preserve';

  public function __construct(string $tag, string $value, string $mode)
  {
    $this->tag = $tag;
    $this->value = $value;
    self::MODE === $mode or throw new InvalidArgumentException("Invalid mode '{$mode}'");
  }

  public function apply(DocBlock $docBlock): DocBlock
  {
    if (0 !== $docBlock->countTheTag($this->tag)) {
      return $docBlock;
    }
    $docBlock->removeTrailingBlankLines();
    $docBlock->pushLine($this->forgeLines());

    return $docBlock;
  }
}
