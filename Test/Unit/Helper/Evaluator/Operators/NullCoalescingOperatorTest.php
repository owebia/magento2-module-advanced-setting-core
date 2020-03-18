<?php
/**
 * Copyright © 2020 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Test\Unit\Helper\Evaluator;

/**
 * Test Null Coalescing Operator
 * https://www.php.net/manual/en/migration70.new-features.php#migration70.new-features.null-coalesce-op
 */
class NullCoalescingOperatorTest extends AbstractTest
{
    /**
     * Test Null Coalescing Operator
     */
    public function testNullCoalescingOperator()
    {
        $this->parse('$a = null; $b = null ?? $a ?? 3;')
            ->assertVariableSame('$a', null)
            ->assertVariableSame('$b', 3);
    }
}
