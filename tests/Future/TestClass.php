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

namespace PrInSt\Symfony\PeselValidator\Tests\Future;

use PrInSt\Symfony\PeselValidator\Constraint\Pesel;

class TestClass
{
    public function __construct(
        #[Pesel]
        public readonly mixed $pesel,
    ) {
        // nothing here
    }
}
