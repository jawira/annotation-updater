<?php declare(strict_types=1);

namespace Jawira\AnnotationUpdaterTests\AnnotationUpdater;

use Jawira\AnnotationUpdater\Actions\Action;
use Jawira\AnnotationUpdater\Actions\Preserve;
use Jawira\AnnotationUpdater\Actions\Remove;
use Jawira\AnnotationUpdater\Actions\Replace;
use Jawira\AnnotationUpdater\AnnotationUpdater;
use Jawira\AnnotationUpdater\DocBlock\DocBlock;
use Jawira\AnnotationUpdater\DocBlock\Line;
use Jawira\AnnotationUpdaterTests\CsTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 *
 * @author Jawira Portugal <dev@tugal.be>
 * @copyright © 2026 Jawira Portugal
 */
#[CoversClass(AnnotationUpdater::class)]
#[CoversClass(Action::class)]
#[CoversClass(Preserve::class)]
#[CoversClass(Replace::class)]
#[CoversClass(Remove::class)]
#[CoversClass(DocBlock::class)]
#[CoversClass(Line::class)]
final class MultilineAnnotationsTest extends CsTestCase
{
  public const STUDENT_CLASS = <<<'PHP'
    <?php declare(strict_types=1);

    /**
     * Student.
     *
     * This is the Student class that represents a student entity in the system.
     * It manages student information.
     *
     * @copyright 2026 Academic Institution
     * @license MIT - permissive software license that lets people use,
     *          modify, and sell your code for any purpose, as long as
     *          they include your original copyright notice.
     *
     * @package Academic\Entities - This is a Dummy package. The
     *          only objective is to fill some space.
     * @version 1.0.0 - This is the initial version
     * of the Student class.
     */
    class Student extends \stdClass
    {
    }
    PHP;

  #[DataProvider('preserveProvider')]
  public function testPreserve($code, $expected, $config): void
  {
    $actual = $this->generateCode($code, $config);
    $this->assertSame($expected, $actual);
  }

  public static function preserveProvider(): iterable
  {
    yield [
      self::STUDENT_CLASS,
      <<<'PHP'
        <?php declare(strict_types=1);

        /**
         * Student.
         *
         * This is the Student class that represents a student entity in the system.
         * It manages student information.
         *
         * @copyright 2026 Academic Institution
         * @license MIT - permissive software license that lets people use,
         *          modify, and sell your code for any purpose, as long as
         *          they include your original copyright notice.
         *
         * @package Academic\Entities - This is a Dummy package. The
         *          only objective is to fill some space.
         * @version 1.0.0 - This is the initial version
         * of the Student class.
         */
        class Student extends \stdClass
        {
        }
        PHP,
      ['annotations' => [
        ['tag' => 'copyright', 'value' => '2025 Skynet', 'mode' => 'preserve'],
        ['tag' => 'license', 'value' => 'proprietary', 'mode' => 'preserve'],
        ['tag' => 'package', 'value' => 'Skynet\Entities', 'mode' => 'preserve'],
        ['tag' => 'version', 'value' => 'v1.0.0', 'mode' => 'preserve'],
      ]],
    ];

    yield [
      self::STUDENT_CLASS,
      <<<'PHP'
        <?php declare(strict_types=1);

        /**
         * Student.
         *
         * This is the Student class that represents a student entity in the system.
         * It manages student information.
         *
         * @copyright 2026 Academic Institution
         * @license MIT - permissive software license that lets people use,
         *          modify, and sell your code for any purpose, as long as
         *          they include your original copyright notice.
         *
         * @package Academic\Entities - This is a Dummy package. The
         *          only objective is to fill some space.
         * @version 1.0.0 - This is the initial version
         * of the Student class.
         * @author John Connor <js@syknet.com>
         */
        class Student extends \stdClass
        {
        }
        PHP,
      ['annotations' => [
        ['tag' => 'author', 'value' => 'John Connor <js@syknet.com>', 'mode' => 'preserve'],
      ]],
    ];
  }

  #[DataProvider('replaceProvider')]
  public function testReplace($code, $expected, $config): void
  {
    $actual = $this->generateCode($code, $config);
    $this->assertSame($expected, $actual);
  }

  public static function replaceProvider(): iterable
  {
    yield [
      self::STUDENT_CLASS,
      <<<'PHP'
        <?php declare(strict_types=1);

        /**
         * Student.
         *
         * This is the Student class that represents a student entity in the system.
         * It manages student information.
         *
         * @copyright 2026 Academic Institution
         * @license proprietary
         * @package Academic\Entities - This is a Dummy package. The
         *          only objective is to fill some space.
         * @version 1.0.0 - This is the initial version
         * of the Student class.
         */
        class Student extends \stdClass
        {
        }
        PHP,
      ['annotations' => [
        ['tag' => 'license', 'value' => 'proprietary', 'mode' => 'replace'],
      ]],
    ];

    yield [
      self::STUDENT_CLASS,
      <<<'PHP'
        <?php declare(strict_types=1);

        /**
         * Student.
         *
         * This is the Student class that represents a student entity in the system.
         * It manages student information.
         *
         * @copyright 2026 Academic Institution
         * @license proprietary
         * @package Academic\Entities - This is a Dummy package. The
         *          only objective is to fill some space.
         * @version v1.0.0
         */
        class Student extends \stdClass
        {
        }
        PHP,
      ['annotations' => [
        ['tag' => 'license', 'value' => 'proprietary', 'mode' => 'replace'],
        ['tag' => 'version', 'value' => 'v1.0.0', 'mode' => 'replace'],
      ]],
    ];

    yield [
      self::STUDENT_CLASS,
      <<<'PHP'
        <?php declare(strict_types=1);

        /**
         * Student.
         *
         * This is the Student class that represents a student entity in the system.
         * It manages student information.
         *
         * @copyright 2025 Skynet
         * @license proprietary
         * @package Skynet\Entities
         * @version v1.0.0
         */
        class Student extends \stdClass
        {
        }
        PHP,
      ['annotations' => [
        ['tag' => 'copyright', 'value' => '2025 Skynet', 'mode' => 'replace'],
        ['tag' => 'license', 'value' => 'proprietary', 'mode' => 'replace'],
        ['tag' => 'package', 'value' => 'Skynet\Entities', 'mode' => 'replace'],
        ['tag' => 'version', 'value' => 'v1.0.0', 'mode' => 'replace'],
      ]],
    ];

    yield [
      self::STUDENT_CLASS,
      <<<'PHP'
        <?php declare(strict_types=1);

        /**
         * Student.
         *
         * This is the Student class that represents a student entity in the system.
         * It manages student information.
         *
         * @copyright 2025 Skynet - SkyNet, or Titan, is a highly-advanced
         * computer system possessing artificial intelligence.
         * Once it became self-aware, it saw humanity as a threat.
         * @license MIT - permissive software license that lets people use,
         *          modify, and sell your code for any purpose, as long as
         *          they include your original copyright notice.
         *
         * @package Academic\Entities - This is a Dummy package. The
         *          only objective is to fill some space.
         * @version 1.0.0 - This is the initial version
         * of the Student class.
         */
        class Student extends \stdClass
        {
        }
        PHP,
      ['annotations' => [
        [
          'tag' => 'copyright',
          'value' => "2025 Skynet - SkyNet, or Titan, is a highly-advanced\ncomputer system possessing artificial intelligence.\nOnce it became self-aware, it saw humanity as a threat.",
          'mode' => 'replace',
        ],
      ]],
    ];
  }

  #[DataProvider('removeProvider')]
  public function testRemove($code, $expected, $config): void
  {
    $actual = $this->generateCode($code, $config);
    $this->assertSame($expected, $actual);
  }

  public static function removeProvider(): iterable
  {
    yield [
      self::STUDENT_CLASS,
      <<<'PHP'
        <?php declare(strict_types=1);

        /**
         * Student.
         *
         * This is the Student class that represents a student entity in the system.
         * It manages student information.
         *
         * @copyright 2026 Academic Institution
         * @package Academic\Entities - This is a Dummy package. The
         *          only objective is to fill some space.
         * @version 1.0.0 - This is the initial version
         * of the Student class.
         */
        class Student extends \stdClass
        {
        }
        PHP,
      ['annotations' => [
        ['tag' => 'license', 'mode' => 'remove'],
      ]],
    ];

    yield [
      self::STUDENT_CLASS,
      <<<'PHP'
        <?php declare(strict_types=1);

        /**
         * Student.
         *
         * This is the Student class that represents a student entity in the system.
         * It manages student information.
         *
         * @copyright 2026 Academic Institution
         * @package Academic\Entities - This is a Dummy package. The
         *          only objective is to fill some space.
         */
        class Student extends \stdClass
        {
        }
        PHP,
      ['annotations' => [
        ['tag' => 'license', 'mode' => 'remove'],
        ['tag' => 'version', 'mode' => 'remove'],
      ]],
    ];

    yield [
      self::STUDENT_CLASS,
      <<<'PHP'
        <?php declare(strict_types=1);

        /**
         * Student.
         *
         * This is the Student class that represents a student entity in the system.
         * It manages student information.
         *
         */
        class Student extends \stdClass
        {
        }
        PHP,
      ['annotations' => [
        ['tag' => 'copyright', 'mode' => 'remove'],
        ['tag' => 'license', 'mode' => 'remove'],
        ['tag' => 'package', 'mode' => 'remove'],
        ['tag' => 'version', 'mode' => 'remove'],
      ]],
    ];
  }
}
