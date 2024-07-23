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

namespace PrInSt\Symfony\PeselValidator\Tests\Unit\Constraint;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PrInSt\Symfony\PeselValidator\Constraint\Pesel;
use PHPUnit\Framework\TestCase;
use PrInSt\Symfony\PeselValidator\Validator\PeselValidator;
use PrInSt\ValidatorPolishPesel\Enum\Gender;
use Symfony\Component\Validator\Constraint;
use Throwable;
use function is_null;
use function strlen;

class PeselTest extends TestCase
{
    /**
     * @param Gender|null       $forGender
     * @param string|null       $message
     * @param array|string|null $groups
     *
     * @return void
     */
    #[DataProvider('dataProvider_test__construct')]
    public function test__construct(
        ?Gender           $forGender = null,
        ?string           $message = null,
        array|string|null $groups = null,
    ): void {
        $result = $exception = null;

        try {
            $result = new Pesel(
                forGender: $forGender,
                message  : $message,
                groups   : $groups
            );
        } catch (Throwable $exception) {
            // nothing here
        }

        self::assertNull($exception);
        self::assertInstanceOf(Pesel::class, $result);
        self::assertInstanceOf(Constraint::class, $result);

        self::assertSame($forGender, $result->forGender);

        if (is_null($message)) {
            self::assertIsString($result->message);
            self::assertGreaterThan(0, strlen($result->message));
        } else {
            self::assertSame($message, $result->message);
        }

        if (is_null($groups)) {
            self::assertSame([Constraint::DEFAULT_GROUP], $result->groups);
        } elseif (is_string($groups)) {
            self::assertSame([$groups], $result->groups);
        } else {
            self::assertSame($groups, $result->groups);
        }
    }

    /**
     * @return array<non-empty-string, array<non-negative-int, Gender|String|array|null>>
     */
    public static function dataProvider_test__construct(): array
    {
        return [
            'forGender: null; message: null; groups: null'   => [],
            'forGender: Female; message: null; groups: null' => [Gender::Female],
            'forGender: Male; message: null; groups: null'   => [Gender::Male],
            'forGender: null; message: abc; groups: null'    => [null, 'abc'],
            'forGender: null; message: null; groups: abc'    => [null, null, 'abc'],
            'forGender: null; message: null; groups: [def]'  => [null, null, ['def']]
        ];
    }

    /**
     * @return void
     */
    #[Depends('test__construct')]
    public function testValidatedBy(): void
    {
        $result = $exception = null;

        try {
            $result = (new Pesel())->validatedBy();
        } catch (Throwable $exception) {
            // nothing here
        }

        self::assertNull($exception);
        self::assertSame(PeselValidator::class, $result);
    }
}
