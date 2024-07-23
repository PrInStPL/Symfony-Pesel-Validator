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

namespace PrInSt\Symfony\PeselValidator\Tests;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbstractUtils
{
    private static $validatorInstance;

    public static function getValidatorInstance(): ValidatorInterface
    {
        return self::$validatorInstance
            ?? self::$validatorInstance
                = Validation::createValidatorBuilder()
                ->enableAttributeMapping()
                ->getValidator()
        ;
    }
}
