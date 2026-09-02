<?php

namespace Jawira\AnnotationUpdaterTests;

use Jawira\AnnotationUpdater\AnnotationUpdater;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;
use SplFileInfo;

abstract class CsTestCase extends TestCase
{
  protected AnnotationUpdater $annotationUpdater;

  protected function setUp(): void
  {
    $this->annotationUpdater = new AnnotationUpdater();
  }

  /**
   * Helper to run fixer.
   */
  public function generateCode(string $code, array $config): string
  {
    $this->annotationUpdater->configure($config);
    $tokens = Tokens::fromCode($code);
    $this->annotationUpdater->fix(new SplFileInfo(__FILE__), $tokens);

    return $tokens->generateCode();
  }
}
