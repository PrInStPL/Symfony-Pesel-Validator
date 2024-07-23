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

namespace PrInSt\Symfony\PeselValidator\Tests\Unit\Validator;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DependsExternal;
use PrInSt\Symfony\PeselValidator\Constraint\Pesel;
use PrInSt\Symfony\PeselValidator\Tests\Unit\Constraint\PeselTest;
use PrInSt\Symfony\PeselValidator\Validator\PeselValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Translation\TranslatorTrait;
use Throwable;

class PeselValidatorTest extends TestCase
{
    #[DataProvider('dataProvider_testValidate')]
    #[DependsExternal(PeselTest::class, 'test__construct')]
    public function testValidate(mixed $value, ?Pesel $constraint = null, ?string $expectedThrowableClass = null): void
    {
        $peselValidator = new PeselValidator();
        $validator = (new ValidatorBuilder())->getValidator();
        $executionContext = new ExecutionContext(
            $validator,
            $validator->startContext(),
            new class () implements TranslatorInterface {
                use TranslatorTrait;
            }
        );
        $peselValidator->initialize($executionContext);

        $exception = null;
        try {
            $peselValidator->validate($value, $constraint ?? new Pesel());
        } catch (Throwable $exception) {
            // nothing here
        }

        if ($expectedThrowableClass) {
            self::assertNotNull($exception);
            self::assertIsObject($exception);
            self::assertInstanceOf(Throwable::class, $exception);
            self::assertInstanceOf($expectedThrowableClass, $exception);
        } else {
            self::assertNull($exception);
        }
    }

    public static function dataProvider_testValidate(): array
    {
        return [
            'null' => [null],
            'empty string' => [''],
            '123 string' => ['123'],
            '132 int' => [
                123,
                null,
                UnexpectedValueException::class
            ],
            'not Stringable class' => [
                new class () {},
                null,
                UnexpectedValueException::class
            ],
            'Stringable class' => [
                new class () implements \Stringable {
                    public function __toString(): string
                    {
                        return 'string';
                    }
                }
            ]
        ];
    }
}
