<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdater\Actions;

use InvalidArgumentException;
use Jawira\AnnotationUpdater\DocBlock\DocBlock;

/**
 * Remove annotation.
 *
 * The {@see Action::$value} property is empty because it's not used in "remove mode".
 *
 * @author Jawira Portugal <dev@tugal.be>
 * @copyright © 2026 Jawira Portugal
 */
final class Remove extends Action
{
  public const MODE = 'remove';

  public function __construct(string $tag, string $mode)
  {
    $this->tag = $tag;
    $this->value = '';
    self::MODE === $mode or throw new InvalidArgumentException("Invalid mode '{$mode}'");
  }

  /**
   * @return array<int, string>
   */
  public function apply(DocBlock $docBlock): DocBlock
  {
    while ($docBlock->countTheTag($this->tag) > 0) {
      $docBlock->removeLastTag($this->tag);
    }

    return $docBlock;
  }

  /**
   * "Remove" mode should not create a docblock if one doesn't exist.
   */
  public function needsDocblock(): bool
  {
    return false;
  }
}
