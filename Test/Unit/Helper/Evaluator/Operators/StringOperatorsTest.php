<?php
/**
 * Copyright © 2020 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Test\Unit\Helper\Evaluator\Operators;

/**
 * Test String Operators
 * https://www.php.net/manual/en/language.operators.string.php
 */
class StringOperatorsTest extends AbstractTest
{
    /**
     * Test Concatenation
     */
    public function testConcatenation()
    {
        $this->parse('$a = "a" . "b";')
            ->assertVariableSame('$a', 'ab');
    }
}
