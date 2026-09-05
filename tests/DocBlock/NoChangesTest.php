<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdaterTests\DocBlock;

use Jawira\AnnotationUpdater\DocBlock\DocBlock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @author Jawira Portugal <dev@tugal.be>
 * @copyright © 2026 Jawira Portugal
 */
#[CoversClass(DocBlock::class)]
#[CoversClass(Line::class)]
class NoChangesTest extends TestCase
{
  #[DataProvider('singleLineProvider')]
  public function testSingleLine($input): void
  {
    $docblock = new DocBlock($input);
    $output = $docblock->getContent();
    $this->assertSame($input, $output);
  }

  public static function singleLineProvider(): array
  {
    return [
      ['/***/'],
      ['/** */'],
      ['/**      */'],
      ['/**Hello*/'],
      ['/** Hello */'],
      ['/**@test*/'],
      ['/** @test */'],
      ['/**@test foo bar baz*/'],
      ['/** @test foo bar baz */'],
    ];
  }

  #[DataProvider('multiLineProvider')]
  public function testMultiLine($input): void
  {
    $docblock = new DocBlock($input);
    $output = $docblock->getContent();
    $this->assertSame($input, $output);
  }

  public static function multiLineProvider(): iterable
  {
    yield [<<<'PHP'
      /**
      */
      PHP];

    yield [<<<'PHP'
      /**
       */
      PHP];

    yield [<<<'PHP'
      /**
       */
      PHP];

    yield [<<<'PHP'
      /**
       *
       */
      PHP];

    yield [<<<'PHP'
      /**
      *
      */
      PHP];

    yield [<<<'PHP'
      /**

      */
      PHP];

    yield [<<<'PHP'
      /********

      *******/
      PHP];

    yield [<<<"PHP"
      /** \t
         * \t
         */
      PHP];

    yield [<<<'PHP'
      /**@test this is a test
      */
      PHP];

    yield [<<<'PHP'
      /**  @test this is a test
      */
      PHP];

    yield [<<<'PHP'
      /**
      @test this is a test */
      PHP];

    yield [<<<'PHP'
      /**
       @test this is a test */
      PHP];

    yield [<<<'PHP'
      /**
           @test this is a test */
      PHP];

    yield [<<<'PHP'
      /**
      * @test this is a test */
      PHP];

    yield [<<<'PHP'
      /**
       * @test this is a test */
      PHP];

    yield [<<<'PHP'
      /**
           * @test this is a test */
      PHP];

    yield [<<<'PHP'
      /**
      *@test this is a test */
      PHP];

    yield [<<<'PHP'
      /**
       *@test this is a test */
      PHP];

    yield [<<<'PHP'
      /**
           *@test this is a test */
      PHP];

    yield [<<<'PHP'
      /**
       * Hello world
       */
      PHP];

    yield [<<<'PHP'
      /**
      * Hello world
      */
      PHP];

    yield [<<<"PHP"
      /** \t
         * \t Hello world
         */
      PHP];

    yield [<<<'PHP'
      /**
      * This is a multi-line
      * description with multiple
      * lines of text.
      */
      PHP];

    yield [<<<'PHP'
      /**
      * First line
      * Second line
      * Third line
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @param string $name Description
      * @param int $age Another description
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @param string $name
      * @param int $age
      * @return bool
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @var string
      * @var int
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @throws Exception
      * @throws RuntimeException
      */
      PHP];

    yield [<<<'PHP'
      /**
      * Summary line
      *
      * Detailed description here
      * that spans multiple lines.
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @author John Doe <john@example.com>
      * @copyright 2024 Company Name
      * @license MIT
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @deprecated This method is deprecated
      * @see SomeClass::someMethod()
      */
      PHP];

    yield [<<<'PHP'
      /**
      * Method with mixed content
      * @param array $data The input data
      * @return array Filtered data
      * @throws InvalidArgumentException
      */
      PHP];

    yield [<<<'PHP'
      /**
      *
      * @param string $name
      *
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @param string $name Description with special chars: !@#$%^&*()
      */
      PHP];

    yield [<<<'PHP'
      /**
      * Description with "quotes" and 'apostrophes'
      * @param string $text
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @param string $url The URL to fetch (e.g., https://example.com)
      */
      PHP];

    yield [<<<'PHP'
      /**
      * Line with trailing spaces
      * Next line
      */
      PHP];

    yield [<<<'PHP'
      /**
       * Indented with two spaces
       * @param string $value
       */
      PHP];

    yield [<<<'PHP'
      /**
      * First line
      *     Indented detail
      * Back to normal
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @method string getName()
      * @method void setName(string $name)
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @property string $name
      * @property int $count
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @template T
      * @template U
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @phpstan-ignore-next-line
      * @psalm-suppress UndefinedProperty
      */
      PHP];

    yield [<<<'PHP'
      /**
      * Empty line above
      *
      * @param string $test
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @param string $test
      *
      * Empty line below
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @param string $test1
      * @param string $test2
      * @param string $test3
      * @param string $test4
      */
      PHP];

    yield [<<<'PHP'
      /*****
      * Star
      * Star
      *****/
      PHP];

    yield [<<<'PHP'
      /**
      *  Double space after asterisk
      *   Triple space after asterisk
      */
      PHP];

    yield [<<<'PHP'
      /**
      *Tab after asterisk
      */
      PHP];

    yield [<<<'PHP'
      /**
      * Mixed    spacing
      * here
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @param \Exception $e
      * @param \stdClass $obj
      */
      PHP];

    yield [<<<'PHP'
      /**
      * {@inheritDoc}
      */
      PHP];

    yield [<<<'PHP'
      /**
      * { @inheritDoc }
      */
      PHP];

    yield [<<<'PHP'
      /**
      * { @link https://example.com }
      */
      PHP];

    yield [<<<'PHP'
      /**
      * This is a very long description that spans across multiple lines
      * and contains a lot of text to test how the parser handles longer
      * docblock content without any issues or problems.
      * @param string $longParameterName
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @todo Implement this feature
      * @fixme This needs to be fixed
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @internal This is for internal use only
      * @final This class cannot be extended
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @api This is part of the public API
      * @category Database
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @version 1.0.0
      * @since 2024-01-01
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @example /path/to/example.php
      * @example Example usage here
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @used-by SomeClass::method()
      * @used-by AnotherClass::method()
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @covers SomeClass::method()
      * @covers AnotherClass
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @dataProvider providerMethod
      * @depends testSomething
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @beforeClass
      * @afterClass
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @expectedException Exception
      * @expectedExceptionMessage error message
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @backupGlobals
      * @backupStaticAttributes
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @codeCoverageIgnore
      * @codeCoverageIgnoreStart
      * @codeCoverageIgnoreEnd
      */
      PHP];

    yield [<<<'PHP'
      /**
      * Annotation with newline in value
      * @param string $text This is a
      *                    multi-line parameter description
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @param array{string, int} $config
      * @param array<string, mixed> $options
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @param callable(string, int): bool $callback
      * @param Closure(): void $closure
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @param non-empty-string $text
      * @param positive-int $count
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @param class-string<Exception> $exceptionClass
      * @param class-string $className
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @param list<string> $items
      * @param list<int> $numbers
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @param null|string $optional
      * @param int|null $nullable
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @param ?string $optional
      * @param ?int $nullable
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @param string ...$variadic
      * @param mixed ...$args
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @param &$reference
      * @param &string $stringReference
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @param string $name Description with
      *                    line continuation
      * @param int $count Another
      *                  multi-line desc
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @return array{0: string, 1: int}
      * @return static
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @method static string doSomething(string $param)
      * @method static void doAnother()
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @property-read string $readOnly
      * @property-write int $writeOnly
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @mixin \stdClass
      * @mixin \Exception
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @implements \Iterator
      * @implements \Countable
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @extends \BaseClass
      * @uses \TraitName
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @package \Jawira\AnnotationUpdater
      * @subpackage Tests
      */
      PHP];

    yield [<<<'PHP'
      /**
      * Unicode characters: 你好世界 🎉
      * Éèë ñ áéíóú
      */
      PHP];

    yield [<<<'PHP'
      /**
       * Special chars: @#$%^&*()_+-=[]{}|;:,.<>?/~`
       * Backslash: \
       * Pipe: |
       */
      PHP];

    yield [<<<'PHP'
      /**



       * Empty docblock with only whitespace
       *
       *
       *
       */
      PHP];

    yield [<<<'PHP'
      /**
      *
      * Only asterisks
      *
      *
      */
      PHP];

    yield [<<<'PHP'
      /**
      *****
      *****
      *****/
      PHP];

    yield [<<<'PHP'
      /****
      *****/
      PHP];

    yield [<<<'PHP'
      /****
      **
      **
      **/
      PHP];

    yield [<<<'PHP'
      /**
      * @
      */
      PHP];

    yield [<<<'PHP'
      /**
       * @
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @
       * @
            */
      PHP];

    yield [<<<'PHP'
      /**
       * Annotation without value
       * @ flag
       * @test
       */
      PHP];

    yield [<<<'PHP'
      /**
       * Annotation with empty value
       * @param
       * @return
       */
      PHP];

    yield [<<<'PHP'
      /**
       * Annotation with only spaces
       * @param
       * @return
       */
      PHP];

    yield [<<<'PHP'
      /**
      * Multiple annotations on same line
      * @param string $a @param int $b
      */
      PHP];

    yield [<<<'PHP'
      /**
       * @param string $a @param int $b @return bool
       */
      PHP];

    yield [<<<'PHP'
      /**
      * Very deeply nested
      *     * indented
      *         * content
      *             * here
      */
      PHP];

    yield [<<<'PHP'
      /**
       * Line with windows line endings
       * Next line
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @param string $name Name with = sign: a=b=c=d
       */
      PHP];

    yield [<<<'PHP'
      /**
          * @param string $path Path with backslashes: C:\Users\Test\file.txt
          */
      PHP];

    yield [<<<'PHP'
      /**
        * @param string $regex Regex pattern: /^[a-z]+$/i
      */
      PHP];

    yield [<<<'PHP'
      /**
      * @param string $json JSON: {"key": "value", "nested": {"a": 1}}
        */
      PHP];

    yield [<<<'PHP'
      /**
       * @param string $xml XML: <tag attr="value"><child/></tag>
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @param string $html HTML: <div class="test"><span>text</span></div>
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @param string $sql SQL: SELECT * FROM users WHERE id = 1
       */
      PHP];

    yield [<<<'PHP'
      /**
       * Nested brackets: { [ ( ) ] }
       * Multiple levels: { { { } } }
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @access private
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @access public
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @access protected
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @access public Internal description
       * with multiple lines
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @author John Doe <john@example.com>
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @author John Doe
       * @author Jane Smith <jane@example.com>
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @author John Doe (https://johndoe.dev)
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @author John Doe
       * @author Jane Smith
       * @author Bob Wilson
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @copyright 2024 Company Name
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @copyright 2020-2024 Company Name
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @copyright 2024 Company Name (https://company.com)
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @copyright 2024 Company Name All rights reserved
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @copyright MIT License
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @deprecated This method is deprecated, use newMethod() instead
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @deprecated Since version 2.0
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @deprecated This method is deprecated.
       * Will be removed in version 3.0
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @deprecated Use AlternativeClass instead
       * @see AlternativeClass
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @deprecated
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @example
       * <?php
       * $object = new ClassName();
       * $result = $object->method();
       * ?>
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @example /examples/basic-usage.php
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @example Basic usage
       * @example Advanced usage with options
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @example <?php echo "test"; ?>
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @example /path/to/example.php Basic example
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @ignore
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @ignore This should be ignored by code analysis
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @ignore PHPCS
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @internal This is for internal use only
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @internal Do not use this method directly
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @internal
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @link https://example.com
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @link https://example.com Documentation
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @link https://github.com/user/repo
       * @link https://docs.example.com
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @link https://example.com/path?query=value#fragment
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @see ClassName::method()
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @see ClassName::method() For more information
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @see ClassName::method()
       * @see AnotherClass::anotherMethod()
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @see https://example.com
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @see {@link https://example.com}
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @since 1.0.0
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @since 1.0.0 Available since version 1.0
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @since 2024-01-01
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @since 1.0.0
       * @deprecated Since 2.0.0
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @tutorial /docs/tutorials/getting-started.md
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @tutorial Getting Started
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @tutorial https://example.com/tutorials/basic
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @version 1.0.0
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @version 1.0.0 Initial release
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @version 2.0.0
       * @version 1.0.0
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @version 1.2.3-beta.1
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @access public
       * @author John Doe <john@example.com>
       * @copyright 2024 Company Name
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @access protected
       * @author Jane Smith
       * @since 1.0.0
       * @version 1.2.0
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @deprecated Use newMethod() instead
       * @see newMethod()
       * @link https://example.com/migration
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @internal
       * @ignore
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @author John Doe
       * @copyright 2024 Company
       * @license MIT
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @example <?php
       * $obj = new MyClass();
       * $result = $obj->doSomething();
       * ?>
       * @since 1.0.0
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @access private
       * @internal
       * @ignore
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @link https://example.com
       * @see https://docs.example.com
       * @tutorial https://tutorials.example.com
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @deprecated Since 2.0, will be removed in 3.0
       * @see AlternativeClass::method()
       * @link https://migration-guide.example.com
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @version 1.0.0
       * @since 2024-01-01
       * @author John Doe
       * @copyright 2024 Company
       * @license MIT
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @access public
       * @author John Doe <john@example.com>
       * @copyright 2024 Company Name
       * @since 1.0.0
       * @version 1.2.3
       * @deprecated Use alternativeMethod()
       * @see alternativeMethod()
       * @link https://example.com
       * @example <?php $obj->alternativeMethod(); ?>
       * @internal
       * @ignore
       * @tutorial /docs/tutorials
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @access  public
       * @author John Doe <john@example.com>
       * @copyright 2024 Company Name
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @access public
       * @author John Doe < john@example.com >
       * @copyright 2024 Company Name
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @access
       * @author
       * @copyright
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @access public Description with spaces
       * @author John Doe
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @access	public
       * @author	John Doe
       */
      PHP];

    yield [<<<'PHP'
      /**
       * Method description
       *
       * @access public
       * @author John Doe
       */
      PHP];

    yield [<<<'PHP'
      /**
       * @access public
       *
       * @author John Doe
       */
      PHP];
  }
}
