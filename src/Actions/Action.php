<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdater\Actions;


use Jawira\AnnotationUpdater\DocBlock\DocBlock;
use Jawira\AnnotationUpdater\DocBlock\Line;

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

  public function forgeLine(): Line
  {
    return new Line(DocBlock::INDENT . ' @' . $this->tag . ' ' . $this->value);
  }
}
