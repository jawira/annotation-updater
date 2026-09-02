<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdater\DocBlock;

readonly class Line
{
  public bool $isOpening;
  public bool $isIndented;
  public bool $isClosing;
  public string $indent;
  public string $content;

  public function __construct(string $content)
  {
    $this->isClosing = \str_ends_with($content, DocBlock::CLOSING);
    if ($this->isClosing) {
      $content = \substr($content, 0, -\strlen(DocBlock::CLOSING));
    }

    $this->isOpening = \str_starts_with($content, DocBlock::OPENING);
    $this->isIndented = 1 === \preg_match('~^\s*\*~', $content, $matches);
    if ($this->isOpening) {
      $this->indent = '';
      $content = substr($content, \strlen(DocBlock::OPENING));
    } elseif ($this->isIndented) {
      $match = \reset($matches);
      \is_string($match) or throw new \Exception('Invalid indentation');
      $this->indent = $match;
      $content = \substr($content, \strlen($this->indent));
    } else {
      $this->indent = '';
    }

    $this->content = $content;
  }

  /**
   * Is composed of whitespace characters, or is empty string.
   */
  public function isBlankLine(): bool
  {
    return 1 === \preg_match('~^\s*$~', $this->content);
  }

  /**
   * Tells if this line has a tag.
   */
  public function isATag(): bool
  {
    return 1 === \preg_match('~^\s*@\S+~', $this->content);
  }

  /**
   * Tells if this line is a tag.
   */
  public function isTheTag(string $tag): bool
  {
    $tag = preg_quote($tag, '~');

    return 1 === \preg_match('~^\s*@' . $tag . '(?=\s|$)~', $this->content);
  }
}
