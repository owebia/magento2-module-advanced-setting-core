<?php
/**
 * Copyright © 2020 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Test\Unit\Helper\Evaluator\Operators;

/**
 * Test Bitwise Operators
 * https://www.php.net/manual/en/language.operators.bitwise.php
 */
class BitwiseOperatorsTest extends AbstractTest
{
    /**
     * Test And
     */
    public function testBitwiseOperators()
    {
        $this->parse('$a = 1 & 5;')
            ->assertVariableSame('$a', 1 & 5);
    }

    /**
     * Test Or (inclusive or)
     */
    public function testOr()
    {
        $this->parse('$a = 1 | 2;')
            ->assertVariableSame('$a', 1 | 2);
    }

    /**
     * Test Xor (exclusive or)
     */
    public function testXor()
    {
        $this->parse('$a = 1 ^ 5;')
            ->assertVariableSame('$a', 1 ^ 5);
    }

    /**
     * Test Not
     */
    public function testNot()
    {
        $this->parse('$a = ~ 1;')
            ->assertVariableSame('$a', ~ 1);
    }

    /**
     * Test Shift left
     */
    public function testShiftLeft()
    {
        $this->parse('$a = 1 << 5;')
            ->assertVariableSame('$a', 1 << 5);
    }

    /**
     * Test Shift right
     */
    public function testShiftRight()
    {
        $this->parse('$a = 1 >> 5;')
            ->assertVariableSame('$a', 1 >> 5);
    }
}
