<?php
/**
 * Copyright © 2020 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Test\Unit\Helper\Evaluator;

class ForeachTest extends AbstractTest
{
    /**
     * Test Foreach
     */
    public function testForeach()
    {
        $this->parse('$a = 0; foreach ([ 1, 2, 3, 4, 5 ] as $b) { $a += $b; }')
            ->assertVariableSame('$a', 15);
    }
}
