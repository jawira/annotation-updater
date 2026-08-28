<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdater;

use Jawira\AnnotationUpdater\Actions\Action;
use Jawira\AnnotationUpdater\Actions\Preserve;
use Jawira\AnnotationUpdater\Actions\Remove;
use Jawira\AnnotationUpdater\Actions\Replace;
use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use function array_slice;
use function count;
use function preg_split;

/**
 * Custom rule to update PHPDoc annotations.
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
    $configuration = $this->getConfigurationDefinition()->resolve($configuration);

    // Mapping to "Annotation" objects
    foreach ($configuration[self::ANNOTATIONS] as $configItem) {
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
   * Iterates through tokens to find class/interface/trait/enum/function declarations
   * and updates their docblocks based on the configured updates.
   */
  protected function applyFix(SplFileInfo $file, Tokens $tokens): void
  {
    if (empty($this->actions)) {
      return;
    }

    for ($index = 0, $count = $tokens->count(); $index < $count; ++$index) {
      $token = $tokens[$index];

      if (!$token->isGivenKind([\T_CLASS, \T_INTERFACE, \T_TRAIT, \T_ENUM])) {
        continue;
      }

      $docBlockIndex = RenderHelper::findDocCommentIndex($tokens, $index);
      if (null === $docBlockIndex) {
        continue;
      }

      $docComment = $tokens[$docBlockIndex]->getContent();
      $updatedDocComment = $this->applyNewAnnotations($docComment);

      if ($updatedDocComment !== $docComment) {
        $tokens[$docBlockIndex] = new Token([\T_DOC_COMMENT, $updatedDocComment]);
      }
    }
  }

  /**
   * Applies all configured annotations to a doc comment.
   */
  private function applyNewAnnotations(string $docComment): string
  {
    $lines = preg_split('/\R/', $docComment) ?: [$docComment];
    $contentLines = array_slice($lines, 1, max(0, count($lines) - 2));

    foreach ($this->actions as $action) {
      $contentLines = $action->apply($contentLines);
    }

    return RenderHelper::rebuildDocComment($contentLines);
  }
}
