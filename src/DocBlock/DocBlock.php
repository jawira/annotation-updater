<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdater\DocBlock;

use Jawira\AnnotationUpdater\RenderHelper;
use function count;

/**
 * Represents and manipulates PHPDoc DocBlock comment strings.
 *
 * This class provides functionality to parse, analyze, and rebuild DocBlock
 * comments while preserving their original formatting.
 *
 * It can handle both single-line and multi-line DocBlock formats and maintains
 * whitespace, asterisk alignment, and content positioning during all
 * operations.
 */
class DocBlock
{
  public const OPENING = '/**';
  public const INDENT = ' *';
  public const CLOSING = '*/';
  const EOL = "\n";

  /**
   * @var array<array-key,\Jawira\AnnotationUpdater\DocBlock\Line>
   */
  private array $lines = [];
  readonly private bool $originallySingleLine;

  public function __construct(string $content)
  {
    $lines = RenderHelper::split($content);
    is_array($lines) or throw new \InvalidArgumentException('Not a valid DocBlock');
    $this->lines = array_map(fn(string $l): Line => new Line($l), $lines);
    $this->originallySingleLine = 1 === count($this->lines);
  }

  public function getContent(): string
  {
    return ($this->originallySingleLine && \count($this->lines) === 1) ? $this->getSingleLineContent() : $this->getMultiLineContent();
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
    $firstKey = \array_key_first($lines);
    $lastKey = \array_key_last($lines);

    foreach ($lines as $key => $line) {

      if ($key === $firstKey) {
        $parts[] = match (true) {
          $line->isOpening => self::OPENING . $line->content,
          $line->isIndented => self::OPENING . self::EOL . $line->indent . $line->content,
          default => self::OPENING . self::EOL . self::INDENT . $line->content,
        };
        continue;
      }

      if ($key === $lastKey) {
        $parts[] = match (true) {
          $line->isClosing && $line->isIndented => $line->indent . $line->content . self::CLOSING,
          $line->isClosing => $line->content . self::CLOSING,
          default => $line->indent . $line->content . self::EOL . ' ' . self::CLOSING,
        };
        continue;
      }

      $parts[] = $line->isIndented ? $line->indent . $line->content : $line->content;
    }

    $content = implode(self::EOL, $parts);

    // If the last line has been removed then it's possible to
    // have a DocBlock without closing string. Let's fix this.
    if (!str_ends_with($content, self::CLOSING)) {
      $content .= self::EOL . ' ' . self::CLOSING;
    }

    return $content;
  }

  /**
   * Return the DocBlock as a single line.
   */
  private function getSingleLineContent(): string
  {
    $line = reset($this->lines);
    $line instanceof Line or throw new \InvalidArgumentException('Invalid Line object');

    return self::OPENING . $line->content . self::CLOSING;
  }

  /**
   * Count how many times a tag is present.
   *
   * @return non-negative-int
   */
  public function countTheTag(string $tag): int
  {
    \strlen(\trim($tag)) !== 0 or throw new \InvalidArgumentException('Annotation cannot be empty string');
    $count = 0;
    foreach ($this->lines as $line) {
      if ($line->isTheTag($tag)) {
        $count++;
      }
    }

    return $count;
  }

  public function lastPositionForTheTag(string $tag): ?int
  {
    \strlen(\trim($tag)) !== 0 or throw new \InvalidArgumentException('Annotation cannot be empty string');
    $position = null;
    foreach (\array_values($this->lines) as $key => $line) {
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
    if ($position === null) {
      return;
    }
    do {
      unset($this->lines[$position]);
      $this->lines = \array_values($this->lines);
    } while (array_key_exists($position, $this->lines) && !$this->lines[$position]->isATag());
  }

  public function removeTrailingBlankLines(): void
  {
    while ($lastLine = end($this->lines)) {
      if (!$lastLine->isBlankLine()) {
        break;
      }
      \array_pop($this->lines);
    }
  }

  /**
   * Add lines at the end of DocBlock.
   *
   * @param \Jawira\AnnotationUpdater\DocBlock\Line[] $lines
   */
  public function pushLine(array $lines): void
  {
    $this->insertLines($lines, count($this->lines));
  }

  /**
   * @param \Jawira\AnnotationUpdater\DocBlock\Line[] $lines
   */
  public function insertLines(array $lines, int $position): void
  {
    \array_splice($this->lines, $position, 0, $lines);
  }
}
