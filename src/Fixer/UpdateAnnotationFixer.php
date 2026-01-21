<?php declare(strict_types=1);

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;

class UpdateAnnotationFixer extends AbstractFixer implements FixerInterface
{
  protected function applyFix(SplFileInfo $file, Tokens $tokens): void
  {
    // TODO: Implement applyFix() method.
  }

  public function isCandidate(Tokens $tokens): bool
  {
    // TODO: Implement isCandidate() method.
  }

  public function getDefinition(): FixerDefinitionInterface
  {
    // TODO: Implement getDefinition() method.
  }
}
