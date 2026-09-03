<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdater;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Methods to manipulate tokens.
 *
 * Contains static method because they are _pure functions_.
 */
class RenderHelper
{

  /**
   * Returns true if class has a DocBlock and false otherwise.
   *
   * Index must point to a "Classy" token.
   */
  static public function hasDocBlock(Tokens $tokens, int $index): bool
  {
    $location = self::findDocBlock($tokens, $index);

    if (!is_int($location)) {
      return false;
    }

    return $tokens[$location]->isGivenKind(\T_DOC_COMMENT);
  }

  /**
   * Returns the DocBlock index if the class has one.
   *
   * When the class has no DocBlock it returns the location where the DocBlock
   * should have been.
   */
  static public function findDocBlock(Tokens $tokens, int $index): ?int
  {
    if (!$tokens[$index]->isClassy()) {
      return null;
    }

    $candidate = $tokens->getPrevNonWhitespace($index);
    while (!is_null($candidate)) {
      if ($tokens[$candidate]->isGivenKind(\T_DOC_COMMENT)) {
        return $candidate;
      }
      if ($tokens[$candidate]->isGivenKind(CT::T_ATTRIBUTE_CLOSE)) {
        $candidate = $tokens->findBlockStart(Tokens::BLOCK_TYPE_ATTRIBUTE, $candidate);
      }
      if (!$tokens[$candidate]->isGivenKind([\T_ATTRIBUTE, \T_FINAL, \T_READONLY, \T_ABSTRACT])) {
        break;
      }
      $index = $candidate;
      $candidate = $tokens->getPrevNonWhitespace($index);
    }

    return $index;
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
