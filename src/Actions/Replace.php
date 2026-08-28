<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdater\Actions;

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

  /**
   * @param array<int, string> $contentLines
   * @return array<int, string>
   */
  public function apply(array $contentLines): array
  {
    $tagLines = $this->findTagLines($contentLines);

    if ([] === $tagLines) {
      $contentLines[] = $this->renderTagLine();

      return $contentLines;
    }

    $firstTagIndex = $tagLines[0];
    $filteredContentLines = [];
    foreach ($contentLines as $index => $line) {
      if (in_array($index, $tagLines, true)) {
        continue;
      }
      $filteredContentLines[] = $line;
    }

    array_splice($filteredContentLines, $firstTagIndex, 0, [$this->renderTagLine()]);

    return $filteredContentLines;
  }
}
