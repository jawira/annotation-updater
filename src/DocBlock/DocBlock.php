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

    return implode(self::EOL, $parts);
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

  /**
   * Tells the first line that contains the provided tag.
   */
  public function firstPositionForTheTag(string $tag): ?int
  {
    \strlen(\trim($tag)) !== 0 or throw new \InvalidArgumentException('Annotation cannot be empty string');
    foreach (\array_values($this->lines) as $key => $line) {
      if ($line->isTheTag($tag)) {
        return $key;
      }
    }

    return null;
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

  public function pushLine(Line $line): void
  {
    \array_push($this->lines, $line);
  }
}
