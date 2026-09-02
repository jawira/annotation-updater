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
