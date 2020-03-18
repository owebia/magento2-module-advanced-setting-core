<?php
/**
 * Copyright © 2020 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Test\Unit\Helper\Evaluator\Operators;

/**
 * Test Assignment Operators - String Operators
 * https://www.php.net/manual/en/language.operators.assignment.php
 * https://www.php.net/manual/en/language.operators.string.php
 */
class AssignmentStringOperatorsTest extends AbstractTest
{
    /**
     * Test Concatenate
     */
    public function testConcatenate()
    {
        $this->parse('$a = "a"; $a .= "b"')
            ->assertVariableSame('$a', 'a' . 'b');
    }
}
