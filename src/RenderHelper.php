<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdater;

use Jawira\AnnotationUpdater\Actions\Action;
use PhpCsFixer\Tokenizer\Tokens;


/**
 * Methods to manipulate tokens.
 *
 * Contains static method because they are _pure functions_.
 */
class RenderHelper
{
  /**
   * Finds the doc comment index immediately before a declaration.
   * Searches backwards from the declaration index, skipping whitespace, attributes, and visibility modifiers.
   */
  public static function findDocCommentIndex(Tokens $tokens, int $declarationIndex): ?int
  {
    $insideAttribute = false;

    for ($index = $declarationIndex - 1; $index >= 0; --$index) {
      $token = $tokens[$index];

      if ($token->isWhitespace()) {
        continue;
      }

      if (']' === $token->getContent()) {
        $insideAttribute = true;
        continue;
      }

      if ($token->isGivenKind(\T_ATTRIBUTE)) {
        $insideAttribute = false;
        continue;
      }

      if ($insideAttribute) {
        continue;
      }

      if ($token->isGivenKind(\T_DOC_COMMENT)) {
        return $index;
      }

      if (!$token->isGivenKind([\T_COMMENT, \T_PUBLIC, \T_PROTECTED, \T_PRIVATE, \T_STATIC, \T_FINAL, \T_ABSTRACT, \T_READONLY])) {
        break;
      }
    }

    return null;
  }


  /**
   * Checks if a doc comment line matches the given tag.
   */
  static public function matchesTag(string $line, Action $annotation): bool
  {
    $trimmedLine = trim($line);
    $trimmedLine = trim((string)preg_replace('/^\*\s*/', '', $trimmedLine));

    if ('' === $trimmedLine) {
      return false;
    }

    if (1 !== preg_match('/^@([A-Za-z0-9_-]+)/', $trimmedLine, $matches)) {
      return false;
    }

    return $matches[1] === $annotation->tag;
  }


  /**
   * Renders a PHPDoc tag line with the given tag and value.
   *
   * @deprecated
   */
  static public function renderTagLine(Action $annotation, string $value): string
  {
    $line = ' * @' . $annotation->tag;

    if ('' !== trim($value)) {
      $line .= ' ' . trim($value);
    }

    return $line;
  }

  /**
   * Rebuilds a complete doc comment from its content lines.
   *
   * @param array<int, string> $contentLines
   */
  static public function rebuildDocComment(array $contentLines): string
  {
    $lines = ['/**'];

    foreach ($contentLines as $line) {
      $lines[] = $line;
    }

    $lines[] = ' */';

    // @todo get newline character from PHP-cs-fixer
    return implode("\n", $lines);
  }

  /**
   * Detects if all class tokens in the given tokens are anonymous classes.
   *
   * Anonymous class uses `\T_CLASS` as normal classes, therefore a special detector is needed.
   */
  public static function isAnonymousClass(Tokens $tokens): bool
  {
    // Check if there are any class tokens
    if (!$tokens->isTokenKindFound(\T_CLASS)) {
      return false;
    }

    // Check all class tokens to see if they are all anonymous
    foreach ($tokens as $index => $token) {
      if ($token->isGivenKind(\T_CLASS)) {
        $prevIndex = $tokens->getPrevMeaningfulToken($index);
        // If any class token is NOT preceded by 'new', it's a named class
        if ($prevIndex === null || $tokens[$prevIndex]->getContent() !== 'new') {
          return false;
        }
      }
    }

    return true;
  }
}
