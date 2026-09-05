<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdater\DocBlock;

use InvalidArgumentException;
use PhpCsFixer\Preg;

use function array_key_exists;
use function array_key_first;
use function array_key_last;
use function array_map;
use function array_pop;
use function array_splice;
use function array_values;
use function count;
use function end;
use function implode;
use function is_array;
use function reset;
use function str_ends_with;
use function strlen;
use function trim;

/**
 * Represents and manipulates PHPDoc DocBlock comment strings.
 *
 * This class provides functionality to parse, analyze, and rebuild DocBlock
 * comments while preserving their original formatting.
 *
 * It can handle both single-line and multi-line DocBlock formats and maintains
 * whitespace, asterisk alignment, and content positioning during all
 * operations.
 *
 * @author Jawira Portugal <dev@tugal.be>
 * @copyright © 2026 Jawira Portugal
 */
class DocBlock
{
  public const OPENING = '/**';
  public const INDENT = ' *';
  public const CLOSING = '*/';
  public const EOL = "\n";
  public const SPACE = ' ';

  /**
   * @var array<array-key,Line>
   */
  private array $lines = [];
  private readonly bool $originallySingleLine;

  public function __construct(string $content)
  {
    $lines = Preg::split('~\R~', $content);
    is_array($lines) or throw new InvalidArgumentException('Not a valid DocBlock');
    $this->lines = array_map(fn (string $l): Line => new Line($l), $lines);
    $this->originallySingleLine = 1 === count($this->lines);
  }

  public function getContent(): string
  {
    return ($this->originallySingleLine && 1 === count($this->lines)) ? $this->getSingleLineContent() : $this->getMultiLineContent();
  }

  /**
   * Count how many times a tag is present.
   *
   * @return non-negative-int
   */
  public function countTheTag(string $tag): int
  {
    0 !== strlen(trim($tag)) or throw new InvalidArgumentException('Annotation cannot be empty string');
    $count = 0;
    foreach ($this->lines as $line) {
      if ($line->isTheTag($tag)) {
        ++$count;
      }
    }

    return $count;
  }

  public function lastPositionForTheTag(string $tag): ?int
  {
    0 !== strlen(trim($tag)) or throw new InvalidArgumentException('Annotation cannot be empty string');
    $position = null;
    foreach (array_values($this->lines) as $key => $line) {
      if ($line->isTheTag($tag)) {
        $position = $key;
      }
    }

    return $position;
  }

  /**
   * Remove the provided tag starting by the end.
   */
  public function removeLastTag(string $tag): void
  {
    $position = $this->lastPositionForTheTag($tag);
    if (null === $position) {
      return;
    }
    do {
      unset($this->lines[$position]);
      $this->lines = array_values($this->lines);
    } while (array_key_exists($position, $this->lines) && !$this->lines[$position]->isATag());
  }

  public function removeTrailingBlankLines(): void
  {
    while ($lastLine = end($this->lines)) {
      if (!$lastLine->isBlankLine()) {
        break;
      }
      array_pop($this->lines);
    }
  }

  /**
   * Add lines at the end of DocBlock.
   *
   * @param Line[] $lines
   */
  public function pushLine(array $lines): void
  {
    $this->insertLines($lines, count($this->lines));
  }

  /**
   * @param Line[] $lines
   */
  public function insertLines(array $lines, int $position): void
  {
    array_splice($this->lines, $position, 0, $lines);
  }

  /**
   * Return MultiLine content.
   *
   * This method is specially long since I am trying to keep the "ugliness" of a PHPDoc.
   * By "ugliness" I refer to malformed DocBlock, with a tag in the first line, wrong indentation, etc.
   * Use other PHP-CS-fixer rules to "beautify" a DocBlock.
   */
  private function getMultiLineContent(): string
  {
    $parts = [];
    $lines = array_values($this->lines);
    $firstKey = array_key_first($lines);
    $lastKey = array_key_last($lines);

    foreach ($lines as $key => $line) {
      if ($key === $firstKey) {
        $parts[] = match (true) {
          $line->isOpening => self::OPENING.$line->content,
          $line->isIndented => self::OPENING.self::EOL.$line->indent.$line->content,
          default => self::OPENING.self::EOL.self::INDENT.$line->content,
        };

        continue;
      }

      if ($key === $lastKey) {
        $parts[] = match (true) {
          $line->isClosing && $line->isIndented => $line->indent.$line->content.self::CLOSING,
          $line->isClosing => $line->content.self::CLOSING,
          default => $line->indent.$line->content.self::EOL.self::SPACE.self::CLOSING,
        };

        continue;
      }

      $parts[] = $line->isIndented ? $line->indent.$line->content : $line->content;
    }

    $content = implode(self::EOL, $parts);

    // If the last line has been removed then it's possible to
    // have a DocBlock without closing string. Let's fix this.
    if (!str_ends_with($content, self::CLOSING)) {
      $content .= self::EOL.self::SPACE.self::CLOSING;
    }

    return $content;
  }

  /**
   * Return the DocBlock as a single line.
   */
  private function getSingleLineContent(): string
  {
    $line = reset($this->lines);
    $line instanceof Line or throw new InvalidArgumentException('Invalid Line object');

    return self::OPENING.$line->content.self::CLOSING;
  }
}
