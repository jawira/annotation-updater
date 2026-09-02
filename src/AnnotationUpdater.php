<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdater;

use Jawira\AnnotationUpdater\Actions\Action;
use Jawira\AnnotationUpdater\Actions\Preserve;
use Jawira\AnnotationUpdater\Actions\Remove;
use Jawira\AnnotationUpdater\Actions\Replace;
use Jawira\AnnotationUpdater\DocBlock\DocBlock;
use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use SplFileInfo;

/**
 * Custom rule to update PHPDoc annotations.
 *
 * @see \PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer
 *
 * @implements ConfigurableFixerInterface<array<string, mixed>, array<string, mixed>>
 */
final class AnnotationUpdater extends AbstractFixer implements ConfigurableFixerInterface
{
  public const ANNOTATIONS = 'annotations';
  public const NAME = 'Jawira/annotation_updater';

  /**
   * @var \Jawira\AnnotationUpdater\Actions\Action[]
   */
  private array $actions = [];

  /**
   * Returns the fixer definition with description and code samples.
   */
  public function getDefinition(): FixerDefinitionInterface
  {
    return new FixerDefinition('Update PHPDoc annotation tags in docblocks.', []);
  }

  /**
   * Returns the name of the fixer.
   */
  public function getName(): string
  {
    return self::NAME;
  }

  /**
   * Using the same priority as {@see \PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer}.
   */
  public function getPriority(): int
  {
    return 10;
  }

  /**
   * Checks if the given tokens are candidates for this fixer.
   * Returns true if tokens contain class, interface, trait, enum, or function declarations.
   */
  public function isCandidate(Tokens $tokens): bool
  {
    return $tokens->isTokenKindFound(\T_CLASS)
      || $tokens->isTokenKindFound(\T_INTERFACE)
      || $tokens->isTokenKindFound(\T_TRAIT)
      || $tokens->isTokenKindFound(\T_ENUM);
  }

  /**
   * Configuration definition.
   */
  public function getConfigurationDefinition(): FixerConfigurationResolverInterface
  {
    return new FixerConfigurationResolver([
      (new FixerOptionBuilder(self::ANNOTATIONS, 'Annotation updates to apply'))
        ->setAllowedTypes(["string[][]"])
        ->setDefault([])
        ->getOption(),
    ]);
  }

  /**
   * Configures the fixer with the provided configuration.
   *
   * @param array<string, mixed>|list<array<string, mixed>>|null $configuration
   */
  public function configure(?array $configuration = null): void
  {
    // New configuration overrides previous one
    $this->actions = [];

    // Fallback
    if (empty($configuration)) {
      $configuration = [self::ANNOTATIONS => []];
    }

    // Resolving configuration
    $this->configuration = $this->getConfigurationDefinition()->resolve($configuration);

    // Mapping to "Annotation" objects
    foreach ($this->configuration[self::ANNOTATIONS] as $configItem) {
      $annotation = match ($configItem['mode']) {
        Preserve::MODE => new Preserve(... $configItem),
        Replace::MODE => new Replace(... $configItem),
        Remove::MODE => new Remove(... $configItem),
        default => throw new \InvalidArgumentException("Cannot instantiate invalid mode '{$configItem['mode']}'")
      };
      $this->actions[] = $annotation;
    }
  }

  /**
   * Applies the fix to the given file's tokens.
   *
   * Iterates through tokens to find class/interface/trait/enum/function declarations
   * and updates their docblocks based on the configured updates.
   */
  protected function applyFix(SplFileInfo $file, Tokens $tokens): void
  {
    if (empty($this->actions)) {
      return;
    }

    $tokensAnalyzer = new TokensAnalyzer($tokens);
    for ($index = $tokens->count() - 1; $index >= 0; --$index) {
      $token = $tokens[$index];

      if (!$token->isClassy()) {
        continue;
      }

      if ($tokensAnalyzer->isAnonymousClass($index)) {
        continue;
      }

      $docBlockIndex = $tokens->getTokenNotOfKindsSibling($index, -1, [\T_WHITESPACE, \T_COMMENT]);
      if (!is_int($docBlockIndex) || !$tokens[$docBlockIndex]->isGivenKind(\T_DOC_COMMENT)) {
        $docblockIsNeeded = array_reduce($this->actions, fn(bool $c, Action $a): bool => $c || $a->needsDocblock(), false);
        if (!$docblockIsNeeded) {
          continue;
        }
        $tokens->insertAt($index, new Token([\T_DOC_COMMENT, DocBlock::OPENING . DocBlock::EOL . ' ' . DocBlock::CLOSING]));
        $index++;
        $tokens->insertAt($index, new Token([\T_WHITESPACE, DocBlock::EOL]));
        $index++;
        $index++;

        continue;
      }
      $index = $docBlockIndex;
      is_int($index) or throw new \Exception("Invalid docblock index '{$docBlockIndex}'"); // Make PHPStan happy!
      unset($docBlockIndex);

      foreach ($this->actions as $action) {
        $oldContent = $tokens[$index]->getContent();
        $newContent = $action->apply(new DocBlock($oldContent))->getContent();
        if ($oldContent !== $newContent) {
          $tokens[$index] = new Token([\T_DOC_COMMENT, $newContent]);
        }
      }
    }
  }
}
