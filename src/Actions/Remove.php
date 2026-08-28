<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdater\Actions;

use Jawira\AnnotationUpdater\RenderHelper;

/**
 * Remove annotation.
 *
 * The {@see \Jawira\AnnotationUpdater\Actions\Action::$value} property is empty because it's not used in "remove mode".
 */
final class Remove extends Action
{
  public const MODE = 'remove';

  public function __construct(string $tag, string $mode)
  {
    $this->tag = $tag;
    $this->value = '';
    $mode === self::MODE or throw new \InvalidArgumentException("Invalid mode '$mode'");
  }

  /**
   * @param array<int, string> $contentLines
   * @return array<int, string>
   */
  public function apply(array $contentLines): array
  {
    return array_values(array_filter(
      $contentLines,
      fn(string $line): bool => !RenderHelper::matchesTag($line, $this),
    ));
  }
}
