<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdater\Actions;

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

  /**
   * @param array<int, string> $contentLines
   * @return array<int, string>
   */
  public function apply(array $contentLines): array
  {
    if ([] !== $this->findTagLines($contentLines)) {
      return $contentLines;
    }

    $contentLines[] = $this->renderTagLine();

    return $contentLines;
  }
}
