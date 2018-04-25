<?php

/*
 * This file is part of the Sami utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sami\Parser\Filter;

use Sami\Reflection\ClassReflection;
use Sami\Reflection\MethodReflection;
use Sami\Reflection\PropertyReflection;

interface FilterInterface
{
    public function acceptClass(ClassReflection $class);

    public function acceptMethod(MethodReflection $method);

    public function acceptProperty(PropertyReflection $property);
}
