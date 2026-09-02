<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdater\Actions;


use Jawira\AnnotationUpdater\DocBlock\DocBlock;

/**
 * Do not modify annotation if it's already present.
 */
final class Preserve extends Action
{
  public const MODE = 'preserve';

  public function __construct(string $tag, string $value, string $mode)
  {
    $this->tag = $tag;
    $this->value = $value;
    $mode === self::MODE or throw new \InvalidArgumentException("Invalid mode '$mode'");
  }

  public function apply(DocBlock $docBlock): DocBlock
  {
    if ($docBlock->countTheTag($this->tag) !== 0) {
      return $docBlock;
    }
    $docBlock->removeTrailingBlankLines();
    $line = $this->forgeLine();
    $docBlock->pushLine($line);

    return $docBlock;
  }
}
