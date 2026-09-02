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


  static public function hasDocBlock(Tokens $tokens, int $index): bool
  {
    $docBlockIndex = self::findDocBlock($tokens, $index);

    return is_int($docBlockIndex);
  }

  /**
   * Get the closest DocBlock for a Class.
   */
  static public function findDocBlock(Tokens $tokens, int $index): ?int
  {
    if (!$tokens[$index]->isClassy()) {
      return null;
    }

    $docBlockIndex = $tokens->getTokenNotOfKindsSibling($index, -1, [\T_WHITESPACE, \T_COMMENT, \T_FINAL, \T_READONLY, \T_ABSTRACT]);
    if (!is_int($docBlockIndex)) {
      return null;
    }

    $isDocBlock = $tokens[$docBlockIndex]->isGivenKind(\T_DOC_COMMENT);

    return $isDocBlock ? $docBlockIndex : null;
  }

  /**
   * Find class start.
   *
   * This is ideal to know where to insert a DocBlock when you are sure your
   * class doesn't have one.
   *
   * Returns null if provided index is not classy.
   */
  static public function findClassStart(Tokens $tokens, int $index): ?int
  {
    if (!$tokens[$index]->isClassy()) {
      return null;
    }

    $start = $index;
    while (--$index >= 0) {
      if ($tokens[$index]->isGivenKind(\T_WHITESPACE)) {
        continue;
      }
      if (!$tokens[$index]->isGivenKind([\T_DOC_COMMENT, \T_FINAL, \T_READONLY, \T_ABSTRACT])) {
        break;
      }
      $start = $index;
    }

    return $start;
  }

  /**
   * Reimplement using {@see \PhpCsFixer\Tokenizer\TokensAnalyzer::isAnonymousClass}.
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

  /**
   * @return string[]
   */
  public static function split(string $text): array
  {
    $lines = \preg_split('~\R~', $text);
    is_array($lines) or throw new \Exception('Error parsing text');

    return $lines;
  }
}
