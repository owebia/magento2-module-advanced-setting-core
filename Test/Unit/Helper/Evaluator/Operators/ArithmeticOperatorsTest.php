<?php
/**
 * Copyright © 2020 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Test\Unit\Helper\Evaluator\Operators;

/**
 * Test Arithmetic Operators
 * https://www.php.net/manual/en/language.operators.arithmetic.php
 */
class ArithmeticOperatorsTest extends AbstractTest
{
    /**
     * Test Identity
     */
    public function testIdentity()
    {
        $this->parse('$a = +7;')
            ->assertVariableSame('$a', +7);
    }

    /**
     * Test Negation
     */
    public function testNegation()
    {
        $this->parse('$a = -7;')
            ->assertVariableSame('$a', -7);
    }

    /**
     * Test Addition
     */
    public function testAddition()
    {
        $this->parse('$a = 7 + 3;')
            ->assertVariableSame('$a', 7 + 3);
    }

    /**
     * Test Subtraction
     */
    public function testSubtraction()
    {
        $this->parse('$a = 7 - 3;')
            ->assertVariableSame('$a', 7 - 3);
    }

    /**
     * Test Multiplication
     */
    public function testMultiplication()
    {
        $this->parse('$a = 7 * 3;')
            ->assertVariableSame('$a', 7 * 3);
    }

    /**
     * Test Division
     */
    public function testDivision()
    {
        $this->parse('$a = 7 / 2;')
            ->assertVariableSame('$a', 7 / 2);
    }

    /**
     * Test Modulo
     */
    public function testModulo()
    {
        $this->parse('$a = 7 % 3;')
            ->assertVariableSame('$a', 7 % 3);
    }

    /**
     * Test Exponentiation
     */
    public function testExponentiation()
    {
        $this->parse('$a = 7 ** 3;')
            ->assertVariableSame('$a', 7 ** 3);
    }
}
