<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdater;

use Exception;
use InvalidArgumentException;
use Jawira\AnnotationUpdater\Actions\Action;
use Jawira\AnnotationUpdater\Actions\Preserve;
use Jawira\AnnotationUpdater\Actions\Remove;
use Jawira\AnnotationUpdater\Actions\Replace;
use Jawira\AnnotationUpdater\DocBlock\DocBlock;
use Override;
use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use SplFileInfo;

use function array_any;
use function is_null;
use function sprintf;

use const T_ABSTRACT;
use const T_ATTRIBUTE;
use const T_CLASS;
use const T_DOC_COMMENT;
use const T_ENUM;
use const T_FINAL;
use const T_INTERFACE;
use const T_READONLY;
use const T_TRAIT;
use const T_WHITESPACE;

/**
 * Custom rule to update PHPDoc annotations.
 *
 * @see       GeneralPhpdocAnnotationRemoveFixer
 *
 * @implements ConfigurableFixerInterface<array<string, mixed>, array<string, mixed>>
 *
 * @author    Jawira Portugal <dev@tugal.be>
 * @copyright © 2026 Jawira Portugal
 */
final class AnnotationUpdater extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
  public const ANNOTATIONS = 'annotations';
  public const NAME = 'Jawira/annotation_updater';
  public array $configuration = [];

  /**
   * @var Action[]
   */
  private array $actions = [];

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Returns the fixer definition with description and code samples.
   */
  #[Override]
  public function getDefinition(): FixerDefinitionInterface
  {
    return new FixerDefinition('🏷️ Update PHPDoc tags.', []);
  }

  /**
   * Returns the name of the fixer.
   */
  #[Override]
  public function getName(): string
  {
    return self::NAME;
  }

  /**
   * Using the same priority as {@see GeneralPhpdocAnnotationRemoveFixer}.
   */
  #[Override]
  public function getPriority(): int
  {
    return 0;
  }

  /**
   * Checks if the given tokens are candidates for this fixer.
   * Returns true if tokens contain class, interface, trait, enum, or function declarations.
   */
  #[Override]
  public function isCandidate(Tokens $tokens): bool
  {
    return $tokens->isTokenKindFound(T_CLASS)
      || $tokens->isTokenKindFound(T_INTERFACE)
      || $tokens->isTokenKindFound(T_TRAIT)
      || $tokens->isTokenKindFound(T_ENUM);
  }

  /**
   * Configuration definition.
   */
  #[Override]
  public function getConfigurationDefinition(): FixerConfigurationResolverInterface
  {
    return new FixerConfigurationResolver([
      (new FixerOptionBuilder(self::ANNOTATIONS, 'List of tags configs.'))
        ->setAllowedTypes(['string[][]'])
        ->setDefault([])
        ->getOption(),
    ]);
  }

  /**
   * Configures the fixer with the provided configuration.
   */
  #[Override]
  public function configure(?array $configuration = null): void
  {
    // New configuration overrides previous one
    $this->actions = [];

    // Fallback
    if (is_null($configuration) || [] === $configuration) {
      $configuration = [self::ANNOTATIONS => []];
    }

    // Resolving configuration
    $this->configuration = $this->getConfigurationDefinition()->resolve($configuration);

    // Mapping to "Action" objects
    /** @var array{tag: string, value: string, mode: string} $configItem */
    foreach ($this->configuration[self::ANNOTATIONS] as $configItem) {
      $mode = $configItem['mode'] ?? 'unknown';

      switch ($mode) {
        case Preserve::getMode():
          $action = new Preserve(...$configItem);

          break;

        case Replace::getMode():
          $action = new Replace(...$configItem);

          break;

        case Remove::getMode():
          /** @var array{tag: string, mode: string} $configItem */
          $action = new Remove(...$configItem);

          break;

        default:
          throw new InvalidArgumentException(sprintf('Invalid annotation mode "%s"', $mode));
      }

      $this->actions[] = $action;
    }
  }

  /**
   * Applies the fix to the given file's tokens.
   *
   * Iterates through tokens to find class/interface/trait/enum/function declarations
   * and updates their docblocks based on the configured updates.
   */
  #[Override]
  protected function applyFix(SplFileInfo $file, Tokens $tokens): void
  {
    if (empty($this->actions)) {
      return;
    }

    for ($index = $tokens->count() - 1; $index >= 0; --$index) {
      if (!$tokens[$index]->isClassy()) {
        continue;
      }

      if ((new TokensAnalyzer($tokens))->isAnonymousClass($index)) {
        continue;
      }

      if (!$this->hasDocBlock($tokens, $index)) {
        if (!$this->isDocBlockNeeded()) {
          continue;
        }
        $startIndex = $this->findDocBlock($tokens, $index);
        $tokens->insertAt($startIndex, new Token([T_DOC_COMMENT, DocBlock::OPENING.DocBlock::EOL.DocBlock::SPACE.DocBlock::CLOSING]));
        $tokens->insertAt($startIndex + 1, new Token([T_WHITESPACE, DocBlock::EOL]));
        unset($startIndex);
        $index += 2;
      }

      $index = $this->findDocBlock($tokens, $index);
      foreach ($this->actions as $action) {
        $oldContent = $tokens[$index]->getContent();
        $newContent = $action->apply(new DocBlock($oldContent))->getContent();
        if ($oldContent !== $newContent) {
          $tokens[$index] = new Token([T_DOC_COMMENT, $newContent]);
        }
      }
    }
  }

  /**
   * Tells if a DocBlock is needed.
   *
   * @example The class has no DocBlock and since you only have {@see Remove}
   * actions, then creating a DocBlock is useless.
   */
  private function isDocBlockNeeded(): bool
  {
    return array_any($this->actions, fn (Action $a): bool => $a->needsDocblock());
  }

  /**
   * Returns true if class has a DocBlock and false otherwise.
   *
   * Index must point to a "Classy" token.
   */
  private function hasDocBlock(Tokens $tokens, int $index): bool
  {
    $location = self::findDocBlock($tokens, $index);

    return $tokens[$location]->isGivenKind(T_DOC_COMMENT);
  }

  /**
   * Returns the DocBlock index if the class has one.
   *
   * When the class has no DocBlock it returns the location where the DocBlock
   * should have been.
   *
   * This only works in "classy" elements: class, trait, interface, enum.
   */
  private function findDocBlock(Tokens $tokens, int $index): int
  {
    if (!$tokens[$index]->isClassy()) {
      throw new Exception('Only can find DocBlock in a classy element.');
    }

    $candidate = $tokens->getPrevNonWhitespace($index);
    while (!is_null($candidate)) {
      if ($tokens[$candidate]->isGivenKind(T_DOC_COMMENT)) {
        return $candidate;
      }
      if ($tokens[$candidate]->isGivenKind(CT::T_ATTRIBUTE_CLOSE)) {
        $candidate = $tokens->findBlockStart(Tokens::BLOCK_TYPE_ATTRIBUTE, $candidate);
      }
      if (!$tokens[$candidate]->isGivenKind([T_ATTRIBUTE, T_FINAL, T_READONLY, T_ABSTRACT])) {
        break;
      }
      $index = $candidate;
      $candidate = $tokens->getPrevNonWhitespace($index);
    }

    return $index;
  }
}
