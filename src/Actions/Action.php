<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdater\Actions;

use Jawira\AnnotationUpdater\DocBlock\DocBlock;
use Jawira\AnnotationUpdater\DocBlock\Line;
use PhpCsFixer\Preg;

use function array_key_first;

/**
 * Abstract Action.
 *
 * Concrete classes are used to read and validate configuration array.
 *
 * @author Jawira Portugal <dev@tugal.be>
 * @copyright © 2026 Jawira Portugal
 */
abstract class Action
{
  public const MODE = '';
  public string $tag;
  public string $value;

  /**
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
   * Convert current {@see Action} is {@see Line} objects.
   *
   * When "content" attribute is composed of multiple lines then multiple lines are returned.
   *
   * @return Line[]
   */
  public function forgeLines(): array
  {
    $values = Preg::split('~\R~', $this->value);
    $lines = [];
    foreach ($values as $key => $value) {
      // First contains the tag.
      if ($key === array_key_first($values)) {
        $lines[] = new Line(DocBlock::INDENT.' @'.$this->tag.DocBlock::SPACE.$value);

        continue;
      }
      // Add DocBlock indentation.
      $lines[] = new Line(DocBlock::INDENT.DocBlock::SPACE.$value);
    }

    return $lines;
  }
}
