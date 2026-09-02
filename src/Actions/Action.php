<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdater\Actions;


use Jawira\AnnotationUpdater\DocBlock\DocBlock;
use Jawira\AnnotationUpdater\DocBlock\Line;
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
  abstract public function apply(DocBlock $docBlock): DocBlock;

  /**
   * Tells if this action should create a docblock when one doesn't exist.
   */
  public function needsDocblock(): bool
  {
    return true;
  }

  /**
   * Convert current {@see \Jawira\AnnotationUpdater\Actions\Action} is {@see \Jawira\AnnotationUpdater\DocBlock\Line} objects.
   *
   * When "content" attribute is composed of multiple lines then multiple lines are returned.
   *
   * @return \Jawira\AnnotationUpdater\DocBlock\Line[]
   */
  public function forgeLines(): array
  {
    $values = RenderHelper::split($this->value);
    $lines = [];
    foreach ($values as $key => $value) {
      // First contains the tag.
      if ($key === array_key_first($values)) {
        $lines[] = new Line(DocBlock::INDENT . ' @' . $this->tag . ' ' . $value);
        continue;
      }
      // Add DocBlock indentation.
      $lines[] = new Line(DocBlock::INDENT . ' ' . $value);
    }

    return $lines;
  }
}
