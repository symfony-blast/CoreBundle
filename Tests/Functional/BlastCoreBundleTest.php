<?php

/*
 * Copyright (C) 2015-2017 Libre Informatique
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Blast\Bundle\CoreBundle\Tests\Functional;

use Blast\Bundle\TestsBundle\Functional\BlastTestCase;

class BlastCoreBundleTest extends BlastTestCase
{
    public function testServicesAreInitializable()
    {
        $this->isServicesAreInitializable('blast');
        /*
         * @todo: enable this:
         * $this->isServicesAreInitializable('sil');
         */
    }
}
