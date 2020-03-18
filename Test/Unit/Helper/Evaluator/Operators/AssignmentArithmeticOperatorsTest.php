<?php
/**
 * Copyright © 2020 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Test\Unit\Helper\Evaluator\Operators;

/**
 * Test Assignment Operators - Arithmetic Operators
 * https://www.php.net/manual/en/language.operators.assignment.php
 * https://www.php.net/manual/en/language.operators.arithmetic.php
 */
class AssignmentArithmeticOperatorsTest extends AbstractTest
{
    /**
     * Test Addition
     */
    public function testAddition()
    {
        $this->parse('$a = 7; $a += 3;')
            ->assertVariableSame('$a', 7 + 3);
    }

    /**
     * Test Subtraction
     */
    public function testSubtraction()
    {
        $this->parse('$a = 7; $a -= 3;')
            ->assertVariableSame('$a', 7 - 3);
    }

    /**
     * Test Multiplication
     */
    public function testMultiplication()
    {
        $this->parse('$a = 7; $a *= 3;')
            ->assertVariableSame('$a', 7 * 3);
    }

    /**
     * Test Division
     */
    public function testDivision()
    {
        $this->parse('$a = 7; $a /= 2;')
            ->assertVariableSame('$a', 7 / 2);
    }

    /**
     * Test Modulo
     */
    public function testModulo()
    {
        $this->parse('$a = 7; $a %= 3;')
            ->assertVariableSame('$a', 7 % 3);
    }

    /**
     * Test Exponentiation
     */
    public function testExponentiation()
    {
        $this->parse('$a = 7; $a **= 3;')
            ->assertVariableSame('$a', 7 ** 3);
    }
}
