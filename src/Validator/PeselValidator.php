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

namespace PrInSt\Symfony\PeselValidator\Validator;

use Override;
use PrInSt\Symfony\PeselValidator\Constraint\Pesel as PeselConstraint;
use PrInSt\ValidatorPolishPesel\Exception\InvalidBirthdateException;
use PrInSt\ValidatorPolishPesel\Exception\InvalidBirthdatePatternException;
use PrInSt\ValidatorPolishPesel\Exception\InvalidFormatException;
use PrInSt\ValidatorPolishPesel\Exception\InvalidGenderException;
use PrInSt\ValidatorPolishPesel\Exception\InvalidGenderPatternException;
use PrInSt\ValidatorPolishPesel\Exception\InvalidPeselException;
use PrInSt\ValidatorPolishPesel\Exception\InvalidWeightsException;
use PrInSt\ValidatorPolishPesel\Pesel;
use Stringable;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

use function is_string;

/**
 * PESEL Constraint Validator
 */
class PeselValidator extends ConstraintValidator
{
    final protected const array EXCEPTION_CODES = [
        InvalidBirthdatePatternException::class => PeselConstraint::ERROR_CODE_BIRTHDATE_PATTERN,
        InvalidBirthdateException::class        => PeselConstraint::ERROR_CODE_BIRTHDATE,
        InvalidFormatException::class           => PeselConstraint::ERROR_CODE_FORMAT,
        InvalidGenderPatternException::class    => PeselConstraint::ERROR_CODE_GENDER_PATTERN,
        InvalidGenderException::class           => PeselConstraint::ERROR_CODE_GENDER,
        InvalidWeightsException::class          => PeselConstraint::ERROR_CODE_WEIGHTS,
    ];



    /**
     * @inheritDoc
     */
    #[Override]
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($constraint instanceof PeselConstraint)) {
            throw new UnexpectedTypeException($constraint, PeselConstraint::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value) && !($value instanceof Stringable)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'string');
        }

        /** @var string|Stringable $value */
        $pesel = new Pesel((string) $value);

        try {
            $pesel->tryIsValidFormat();
        } catch (InvalidFormatException $e) {
            $this
                ->context
                ->buildViolation($constraint->message)
                ->setCause($e->getMessage())
                ->setCode(self::EXCEPTION_CODES[$e::class] ?? PeselConstraint::ERROR_CODE_FORMAT)
                ->setParameters([
                    '{{ value }}'     => $value,
                    '{{ violation }}' => $e->getMessage(),
                ])
                ->addViolation()
            ;

            return;
        }

        try {
            $pesel->tryIsValidWeights();
        } catch (InvalidPeselException $e) {
            $this
                ->context
                ->buildViolation($constraint->message)
                ->setCause($e->getMessage())
                ->setCode(self::EXCEPTION_CODES[$e::class] ?? PeselConstraint::ERROR_CODE)
                ->setParameters([
                    '{{ value }}'     => $value,
                    '{{ violation }}' => $e->getMessage(),
                ])
                ->addViolation()
            ;
        }

        try {
            $pesel->tryIsValidBirthdate();
        } catch (InvalidBirthdateException $e) {
            $this
                ->context
                ->buildViolation($constraint->message)
                ->setCause($e->getMessage())
                ->setCode(self::EXCEPTION_CODES[$e::class] ?? PeselConstraint::ERROR_CODE_BIRTHDATE)
                ->setParameters([
                    '{{ value }}'     => $value,
                    '{{ violation }}' => $e->getMessage(),
                ])
                ->addViolation()
            ;
        }

        try {
            $pesel->tryIsValidGender($constraint->forGender);
        } catch (InvalidGenderException $e) {
            $this
                ->context
                ->buildViolation($constraint->message)
                ->setCause($e->getMessage())
                ->setCode(self::EXCEPTION_CODES[$e::class] ?? PeselConstraint::ERROR_CODE_GENDER)
                ->setParameters([
                    '{{ value }}'     => $value,
                    '{{ violation }}' => $e->getMessage(),
                ])
                ->addViolation()
            ;
        }
    }
}
