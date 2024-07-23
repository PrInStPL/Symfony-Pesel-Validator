<?php

/*
 * This file is part of the Polish PESEL number validator package for Symfony.
 *
 * (c) Łukasz Konarski <prinst.pl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrInSt\Symfony\PeselValidator\Constraint;

use Attribute;
use Override;
use PrInSt\Symfony\PeselValidator\Validator\PeselValidator;
use PrInSt\ValidatorPolishPesel\Enum\Gender;
use Symfony\Component\Validator\Constraint;

use function is_null;
use function is_string;

/**
 * PESEL Constraint
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Pesel extends Constraint
{
    public string $message = 'The PESEL is invalid.';



    public const string ERROR_CODE = 'b5ee374b-b83b-4f78-afcc-220a8b215668';
    public const string ERROR_CODE_BIRTHDATE = 'bb089e37-9881-4ce9-a1de-8c3afd4608ea';
    public const string ERROR_CODE_BIRTHDATE_PATTERN = '5c9eb66e-9c31-4899-85dd-523b90dc5e66';
    public const string ERROR_CODE_FORMAT = 'eb431b3e-2639-42e5-9d4b-b8288fd3a7b3';
    public const string ERROR_CODE_GENDER = '5996eacb-2053-4e90-8eb1-57831a221b8b';
    public const string ERROR_CODE_GENDER_PATTERN = 'ad0a9a88-ed53-4948-ba9c-bf32751bfd82';
    public const string ERROR_CODE_WEIGHTS = '128a2726-7280-4d4a-9816-00f9ec4534dc';
    protected const array ERROR_NAMES = [
        self::ERROR_CODE                   => 'INVALID_PESEL',
        self::ERROR_CODE_BIRTHDATE         => 'INVALID_PESEL_BIRTHDATE',
        self::ERROR_CODE_BIRTHDATE_PATTERN => 'INVALID_PESEL_BIRTHDATE_PATTERN',
        self::ERROR_CODE_FORMAT            => 'INVALID_PESEL_FORMAT',
        self::ERROR_CODE_GENDER            => 'INVALID_PESEL_GENDER',
        self::ERROR_CODE_GENDER_PATTERN    => 'INVALID_PESEL_GENDER_PATTERN',
        self::ERROR_CODE_WEIGHTS           => 'INVALID_PESEL_WEIGHTS',
    ];



    /**
     * @param Gender|null          $forGender
     * @param string|null          $message Will replace '{{ value }}' and '{{ violation }}' if found in a string.
     * @param mixed                $options
     * @param string|string[]|null $groups
     * @param mixed                $payload
     *
     * @inheritDoc
     */
    public function __construct(
        public readonly ?Gender $forGender = null,
        ?string                 $message = null,
        mixed                   $options = null,
        array|string|null       $groups = null,
        mixed                   $payload = null
    ) {
        if (!is_null($message)) {
            $this->message = $message;
        }

        if (is_string($groups)) {
            $groups = [$groups];
        }

        parent::__construct($options, $groups, $payload);
    }

    /**
     * @inheritDoc
     * @noinspection PhpMissingParentCallCommonInspection
     */
    #[Override]
    public function validatedBy(): string
    {
        return PeselValidator::class;
    }
}
