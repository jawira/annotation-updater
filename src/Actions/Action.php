<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdater\Actions;

use Jawira\AnnotationUpdater\RenderHelper;

/**
 * Abstract Action.
 *
 * Concrete classes are used to read and validate configuration array.
 */
abstract class Action
{
  public const MODE = '';
  public string $tag;
  public string $value;

  /**
   * @param array<int, string> $contentLines
   * @return array<int, string>
   */
  abstract public function apply(array $contentLines): array;

  public function renderTagLine(): string
  {
    $prefix = ' * @';
    if (empty(trim($this->value))) {
      return "$prefix{$this->tag}";
    }

    return "$prefix{$this->tag} {$this->value}";
  }

  /**
   * @param array<int, string> $contentLines
   * @return list<int>
   */
  protected function findTagLines(array $contentLines): array
  {
    $tagLines = [];

    foreach ($contentLines as $index => $line) {
      if (RenderHelper::matchesTag($line, $this)) {
        $tagLines[] = $index;
      }
    }

    return $tagLines;
  }

  /**
   * Tells if this action should create a docblock when one doesn't exist.
   */
  public function needsDocblock(): bool
  {
    return true;
  }
}
