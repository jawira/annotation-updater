<?php

namespace Jawira\AnnotationUpdaterTests;

use Jawira\AnnotationUpdater\AnnotationUpdater;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;
use SplFileInfo;

abstract class CsTestCase extends TestCase
{
  protected AnnotationUpdater $fixer;

  protected function setUp(): void
  {
    $this->fixer = new AnnotationUpdater();
  }

  /**
   * Helper to run fixer.
   */
  public function generateCode(string $code, array $config): string
  {
    $this->fixer->configure($config);
    $tokens = Tokens::fromCode($code);
    $this->fixer->fix(new SplFileInfo(__FILE__), $tokens);

    return $tokens->generateCode();
  }
}
