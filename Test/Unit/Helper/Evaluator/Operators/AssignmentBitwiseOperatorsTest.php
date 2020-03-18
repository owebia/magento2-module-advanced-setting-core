<?php
/**
 * Copyright © 2020 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Test\Unit\Helper\Evaluator\Operators;

/**
 * Test Assignment Operators - Bitwise Operators
 * https://www.php.net/manual/en/language.operators.assignment.php
 * https://www.php.net/manual/en/language.operators.bitwise.php
 */
class AssignmentBitwiseOperatorsTest extends AbstractTest
{
    /**
     * Test Bitwise And
     */
    public function testAnd()
    {
        $this->parse('$a = 1; $a &= 5;')
            ->assertVariableSame('$a', 1 & 5);
    }

    /**
     * Test Bitwise Or
     */
    public function testBitwiseOr()
    {
        $this->parse('$a = 1; $a |= 2;')
            ->assertVariableSame('$a', 1 | 2);
    }

    /**
     * Test Bitwise Xor
     */
    public function testBitwiseXor()
    {
        $this->parse('$a = 1; $a ^= 5;')
            ->assertVariableSame('$a', 1 ^ 5);
    }

    /**
     * Test Shift left
     */
    public function testShiftLeft()
    {
        $this->parse('$a = 1; $a <<= 5;')
            ->assertVariableSame('$a', 1 << 5);
    }

    /**
     * Test Shift right
     */
    public function testShiftRight()
    {
        $this->parse('$a = 1; $a >>= 5;')
            ->assertVariableSame('$a', 1 >> 5);
    }
}
