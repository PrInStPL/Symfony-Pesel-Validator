<?php

/*
 * This file is part of the Polish PESEL number validator package for Symfony.
 *
 * (c) Łukasz Konarski (PrInSt) <prinst.pl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrInSt\Symfony\PeselValidator\Tests\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\DependsExternal;
use PHPUnit\Framework\TestCase;
use PrInSt\Symfony\PeselValidator\Constraint\Pesel;
use PrInSt\Symfony\PeselValidator\Tests\AbstractUtils;
use PrInSt\Symfony\PeselValidator\Tests\Future\TestBothClass;
use PrInSt\Symfony\PeselValidator\Tests\Future\TestClass;
use PrInSt\Symfony\PeselValidator\Tests\Future\TestFemaleClass;
use PrInSt\Symfony\PeselValidator\Tests\Future\TestMaleClass;
use PrInSt\Symfony\PeselValidator\Tests\Unit\Constraint\PeselTest;
use PrInSt\Symfony\PeselValidator\Tests\Unit\Validator\PeselValidatorTest;
use PrInSt\ValidatorPolishPesel\Enum\Gender;
use Throwable;

class ValidationTest extends TestCase
{
    /**
     * @param mixed $value
     * @param Pesel $constraint
     * @param bool  $valid
     *
     * @return void
     */
    #[DataProvider('dataProvider_testValidationProperty')]
    #[DependsExternal(PeselTest::class, 'test__construct')]
    #[DependsExternal(PeselValidatorTest::class, 'testValidate')]
    public function testValidationProperty(mixed $value, Pesel $constraint, bool $valid): void
    {
        $violationList = $exception = null;
        try {
            $violationList = AbstractUtils::getValidatorInstance()->validate($value, $constraint);
        } catch (Throwable $exception) {
            // nothing here
        }

        self::assertNull($exception);
        if ($valid) {
            self::assertSame(0, $violationList->count());
        } else {
            self::assertGreaterThan(0, $violationList->count());
        }
    }

    /**
     * @return array<non-empty-string, array<>>
     */
    public static function dataProvider_testValidationProperty(): array
    {
        return [
            '123 int; valid: false' => [123, new Pesel(), false],
            '123 string; valid: false' => ['123', new Pesel(), false],
            '00891305664 int; valid: false' => [891305664, new Pesel(), false],
            '00891305664 int; for: Female; valid: false' => [891305664, new Pesel(Gender::Female), false],
            '00891305664 int; for: Male; valid: false' => [891305664, new Pesel(Gender::Male), false],
            '00891305664 string; valid: true' => ['00891305664', new Pesel(), true],
            '00891305664 string; for: Female; valid: true' => ['00891305664', new Pesel(Gender::Female), true],
            '00891305664 string; for: Male; valid: false' => ['00891305664', new Pesel(Gender::Male), false],
            '00831928997 string; valid: true' => ['00831928997', new Pesel(), true],
            '00831928997 string; for: Female; valid: false' => ['00831928997', new Pesel(Gender::Female), false],
            '00831928997 string; for: Male; valid: true' => ['00831928997', new Pesel(Gender::Male), true],
        ];
    }

    #[DataProvider('dataProvider_testValidationAttribute')]
    #[Depends('testValidationProperty')]
    public function testValidationAttribute(TestClass $obj, bool $valid): void
    {
        $violationList = $exception = null;
        try {
            $violationList = AbstractUtils::getValidatorInstance()->validate($obj);
        } catch (Throwable $exception) {
            // nothing here
        }

        self::assertNull($exception);
        if ($valid) {
            self::assertSame(0, $violationList->count());
        } else {
            self::assertGreaterThan(0, $violationList->count());
        }
    }

    /**
     * @return array<non-empty-string, array<>>
     */
    public static function dataProvider_testValidationAttribute(): array
    {
        return [
            'TestClass(00000000000 string); valid: false' => [
                new TestClass('00000000000'),
                false,
            ],
            'TestBothClass(00000000000 string); valid: false' => [
                new TestBothClass('00000000000', '00000000000', '00000000000'),
                false,
            ],
            'TestFemaleClass(00000000000 string); valid: false' => [
                new TestFemaleClass('00000000000', '00000000000'),
                false,
            ],
            'TestMaleClass(00000000000 string); valid: false' => [
                new TestMaleClass('00000000000', '00000000000'),
                false,
            ],

            'TestClass(00891305664 string); valid: true' => [
                new TestClass('00891305664'),
                true,
            ],
            'TestBothClass(00891305664 string); valid: false' => [
                new TestBothClass('00891305664', '00891305664', '00891305664'),
                false,
            ],
            'TestFemaleClass(00891305664 string); valid: true' => [
                new TestFemaleClass('00891305664', '00891305664'),
                true,
            ],
            'TestMaleClass(00891305664 string); valid: false' => [
                new TestMaleClass('00891305664', '00891305664'),
                false,
            ],

            'TestClass(00831928997 string); valid: true' => [
                new TestClass('00831928997'),
                true,
            ],
            'TestBothClass(00831928997 string); valid: false' => [
                new TestBothClass('00831928997', '00831928997', '00831928997'),
                false,
            ],
            'TestFemaleClass(00831928997 string); valid: false' => [
                new TestFemaleClass('00831928997', '00831928997'),
                false,
            ],
            'TestMaleClass(00831928997 string); valid: true' => [
                new TestMaleClass('00831928997', '00831928997'),
                true,
            ],

            'TestBothClass(00810659414 string, 00891305664 string, 00831928997 string); valid: true' => [
                new TestBothClass('00810659414', '00891305664', '00831928997'),
                true,
            ],
            'TestBothClass(00810659414 string, 00831928997 string, 00891305664 string); valid: false' => [
                new TestBothClass('00810659414', '00831928997', '00891305664'),
                false,
            ],

            'TestBothClass(123 string, 00891305664 string, 00831928997 string); valid: false' => [
                new TestBothClass('123', '00891305664', '00831928997'),
                false,
            ],
            'TestBothClass(123 int, 00891305664 string, 00831928997 string); valid: false' => [
                new TestBothClass(123, '00891305664', '00831928997'),
                false,
            ],
        ];
    }
}
