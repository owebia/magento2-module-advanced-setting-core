<?php
/**
 * Copyright © 2020 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Test\Unit\Helper\Evaluator\Operators;

/**
 * Test Bitwise Operators
 * https://www.php.net/manual/en/language.operators.bitwise.php
 * @phpcs:disable Squiz.Operators.ValidLogicalOperators.NotAllowed
 */
class LogicalOperatorsTest extends AbstractTest
{
    /**
     * Test And
     */
    public function testAnd()
    {
        $this->parse('$a = (true and true); $b = (true and false); $c = (false and true); $d = (false and false);')
            ->assertVariableSame('$a', true and true)
            ->assertVariableSame('$b', true and false)
            ->assertVariableSame('$c', false and true)
            ->assertVariableSame('$d', false and false);
    }

    /**
     * Test Or
     */
    public function testOr()
    {
        $this->parse('$a = (true or true); $b = (true or false); $c = (false or true); $d = (false or false);')
            ->assertVariableSame('$a', true or true)
            ->assertVariableSame('$b', true or false)
            ->assertVariableSame('$c', false or true)
            ->assertVariableSame('$d', false or false);
    }

    /**
     * Test Xor
     */
    public function testXor()
    {
        $this->parse('$a = (true xor true); $b = (true xor false); $c = (false xor true); $d = (false xor false);')
            ->assertVariableSame('$a', true xor true)
            ->assertVariableSame('$b', true xor false)
            ->assertVariableSame('$c', false xor true)
            ->assertVariableSame('$d', false xor false);
    }

    /**
     * Test Not
     */
    public function testNot()
    {
        $this->parse('$a = !true; $b = !false;')
            ->assertVariableSame('$a', !true)
            ->assertVariableSame('$b', !false);
    }

    /**
     * Test And &&
     */
    public function testAnd2()
    {
        $this->parse('$a = true && true; $b = true && false; $c = false && true; $d = false && false;')
            ->assertVariableSame('$a', true && true)
            ->assertVariableSame('$b', true && false)
            ->assertVariableSame('$c', false && true)
            ->assertVariableSame('$d', false && false);
    }

    /**
     * Test Or ||
     */
    public function testOr2()
    {
        $this->parse('$a = true || true; $b = true || false; $c = false || true; $d = false || false;')
            ->assertVariableSame('$a', true || true)
            ->assertVariableSame('$b', true || false)
            ->assertVariableSame('$c', false || true)
            ->assertVariableSame('$d', false || false);
    }
}
