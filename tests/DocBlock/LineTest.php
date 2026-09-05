<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdaterTests\DocBlock;

use Jawira\AnnotationUpdater\DocBlock\Line;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @author Jawira Portugal <dev@tugal.be>
 * @copyright © 2026 Jawira Portugal
 */
#[CoversClass(Line::class)]
class LineTest extends TestCase
{
  #[DataProvider('isATagProvider')]
  public function testIsATag(string $input, bool $expected): void
  {
    $line = new Line($input);
    $actual = $line->isATag();
    $this->assertSame($expected, $actual);
  }

  public static function isATagProvider(): array
  {
    return [
      ['/**@test */', true],
      ['/** @test */', true],
      ['/** @test text */', true],
      ['/**@test', true],
      ['/** @test', true],
      ['/** @test text', true],
      ['*@test', true],
      [' * @test', true],
      [' * @test text', true],
      ['  *  @test', true],
      ['  *  @test  ', true],
      ['  *  @testing', true],
      ['  *  @testing  ', true],
      ['  *  @hana dul set net  ', true],
      [' * @\Doctrine\Orm\Mapper\Entity()', true],
      [' * @\Doctrine\Orm\Mapper\Entity() This is a test', true],
      ['@test', true],
      ['  @test', true],
      ['  @test  ', true],
      ['  @testing', true],
      ['  @testing  ', true],
      ['/**test', false],
      ['/** test', false],
      ['/** test  ', false],
      ['  /**  @test', false],
      ['  /**  @test  ', false],
      ['  /**  @testing', false],
      ['  /**  @testing  ', false],
      ['  /**  @hana dul set net  ', false],
      [' /** @\Doctrine\Orm\Mapper\Entity()', false],
      [' /** @\Doctrine\Orm\Mapper\Entity() This is a test', false],
      ['/@test', false],
      ['/ @test', false],
      [' / @test  ', false],
      ['**@test', false],
      ['** @test', false],
      ['** @test text', false],
      ['  **  @test', false],
      ['  **  @test  ', false],
      ['  **  @testing', false],
      ['  **  @testing  ', false],
      ['  **  @hana dul set net  ', false],
      [' ** @\Doctrine\Orm\Mapper\Entity()', false],
      [' ** @\Doctrine\Orm\Mapper\Entity() This is a test', false],
    ];
  }

  #[DataProvider('isTheTagProvider')]
  public function testIsTheTag(string $input, string $tag, bool $expected): void
  {
    $line = new Line($input);
    $actual = $line->isTheTag($tag);
    $this->assertSame($expected, $actual);
  }

  public static function isTheTagProvider(): array
  {
    return [
      ['/** @test', 'test', true],
      ['/**  @test', 'test', true],
      ['/**    @test', 'test', true],
      ['/**    @test  ', 'test', true],
      ['/**    @test this line', 'test', true],
      ['/**    @test $var line', 'test', true],
      ['/**    @test $var line', 'test', true],
      ['/** @\Doctrine\Orm\Mapper\Entity()', '\Doctrine\Orm\Mapper\Entity()', true],
      ['/** @test */', 'test', true],
      ['/**  @test */', 'test', true],
      ['/**    @test */', 'test', true],
      ['/**    @test  */', 'test', true],
      ['/**    @test this line */', 'test', true],
      ['/**    @test $var line */', 'test', true],
      ['/**    @test $var line */', 'test', true],
      ['/** @\Doctrine\Orm\Mapper\Entity() */', '\Doctrine\Orm\Mapper\Entity()', true],
      ['*@test', 'test', true],
      [' * @test', 'test', true],
      ['  *  @test', 'test', true],
      ['  *  @test  ', 'test', true],
      ['  *  @test this line', 'test', true],
      ['  *  @test $var line', 'test', true],
      ['  *  @test $var line', 'test', true],
      [' * @\Doctrine\Orm\Mapper\Entity()', '\Doctrine\Orm\Mapper\Entity()', true],
      ['@test', 'test', true],
      ['  @test', 'test', true],
      ['  @test  ', 'test', true],
      ['  @testing', 'test', false],
      ['  @testing  ', 'test', false],
      ['  *  @testing', 'test', false],
      ['  *  @testing  ', 'test', false],
      [' * ', 'test', false],
      [' * Hello', 'test', false],
      [' * Hello world', 'test', false],
      [' ', 'test', false],
      [' Hello', 'test', false],
      [' Hello world', 'test', false],
    ];
  }

  #[DataProvider('isBlankLineProvider')]
  public function testIsBlankLine(string $input, bool $expected): void
  {
    $line = new Line($input);
    $actual = $line->isBlankLine();
    $this->assertSame($expected, $actual);
  }

  public static function isBlankLineProvider()
  {
    return [
      ['/**', true],
      ['/** */', true],
      ["/** \t */", true],
      [' * @author John Doe', false],
      ['/** ', true],
      ['/**  ', true],
      ["/**\t", true],
      [' */', true],
      ['  */', true],
      ["\t*/", true],
      [' *', true],
      [' * ', true],
      ['  *', true],
      ['  * ', true],
      ['   *', true],
      ['   * ', true],
      ["\t*", true],
      ["*\t", true],
      [" *\t", true],
      ["\t*\t", true],
      [' ', true],
      ['  ', true],
      ['   ', true],
      ["\t", true],
      ["\t\t", true],
      [" \t ", true],
      [" \t\t ", true],
      ["\n", true],
      ["\r", true],
      ["\r\n", true],
      ["\t\n\r ", true],
      ['****/', false],
      [' * Hello', false],
      [' * Hello world', false],
      [' * @param string $name', false],
      [' * @return void', false],
      [' * @var int', false],
      [' * Description text', false],
      [' *This is text', false],
      ['* This is text', false],
      [' ** This is text', false],
      ['/**@test', false],
      ['/** @test', false],
      [' * @test', false],
      ['@test', false],
      [' * @', false],
      [' * @ ', false],
      ['text', false],
      ['Hello world', false],
      ['Some content', false],
      [' * Some content', false],
      ['/** Some content', false],
      ['Some content */', false],
    ];
  }
}
