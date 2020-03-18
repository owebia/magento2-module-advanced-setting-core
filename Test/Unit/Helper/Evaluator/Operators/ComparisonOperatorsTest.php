<?php
/**
 * Copyright © 2020 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Test\Unit\Helper\Evaluator\Operators;

/**
 * Test Comparison Operators
 * https://www.php.net/manual/en/language.operators.comparison.php
 */
class ComparisonOperatorsTest extends AbstractTest
{
    /**
     * Test Equal
     */
    public function testEqual()
    {
        $this->parse('$a = 1; $b = $a == 1; $c = $a == 2;')
            ->assertVariableSame('$a', $a = 1)
            ->assertVariableSame('$b', $a == 1)
            ->assertVariableSame('$c', $a == 2);
    }

    /**
     * Test Identical
     */
    public function testIdentical()
    {
        $this->parse('$a = 1; $b = $a === 1; $c = $a === true;')
            ->assertVariableSame('$a', $a = 1)
            ->assertVariableSame('$b', $a === 1)
            ->assertVariableSame('$c', $a === true);
    }

    /**
     * Test Not equal !=
     */
    public function testNotEqual1()
    {
        $this->parse('$a = 1; $b = $a != 1; $c = $a != 2;')
            ->assertVariableSame('$a', $a = 1)
            ->assertVariableSame('$b', $a != 1)
            ->assertVariableSame('$c', $a != 2);
    }

    /**
     * Test Not equal <>
     */
    public function testNotEqual2()
    {
        $this->parse('$a = 1; $b = $a <> 1; $c = $a <> 2;')
            ->assertVariableSame('$a', $a = 1)
            ->assertVariableSame('$b', $a <> 1)
            ->assertVariableSame('$c', $a <> 2);
    }

    /**
     * Test Not identical
     */
    public function testNotIdentical()
    {
        $this->parse('$a = 1; $b = $a !== 1; $c = $a !== true;')
            ->assertVariableSame('$a', $a = 1)
            ->assertVariableSame('$b', $a !== 1)
            ->assertVariableSame('$c', $a !== true);
    }

    /**
     * Test Less than
     */
    public function testLessThan()
    {
        $this->parse('$a = 2; $b = $a < 1; $c = $a < 2; $d = $a < 3;')
            ->assertVariableSame('$a', $a = 2)
            ->assertVariableSame('$b', $a < 1)
            ->assertVariableSame('$c', $a < 2)
            ->assertVariableSame('$d', $a < 3);
    }

    /**
     * Test Greater than
     */
    public function testGreaterThan()
    {
        $this->parse('$a = 2; $b = $a > 1; $c = $a > 2; $d = $a > 3;')
            ->assertVariableSame('$a', $a = 2)
            ->assertVariableSame('$b', $a > 1)
            ->assertVariableSame('$c', $a > 2)
            ->assertVariableSame('$d', $a > 3);
    }

    /**
     * Test Less than or equal to
     */
    public function testLessThanOrEqualTo()
    {
        $this->parse('$a = 2; $b = $a <= 1; $c = $a <= 2; $d = $a <= 3;')
            ->assertVariableSame('$a', $a = 2)
            ->assertVariableSame('$b', $a <= 1)
            ->assertVariableSame('$c', $a <= 2)
            ->assertVariableSame('$d', $a <= 3);
    }

    /**
     * Test Greater than or equal to
     */
    public function testGreaterThanOrEqualTo()
    {
        $this->parse('$a = 2; $b = $a >= 1; $c = $a >= 2; $d = $a >= 3;')
            ->assertVariableSame('$a', $a = 2)
            ->assertVariableSame('$b', $a >= 1)
            ->assertVariableSame('$c', $a >= 2)
            ->assertVariableSame('$d', $a >= 3);
    }

    /**
     * Test Spaceship
     */
    public function testSpaceship()
    {
        $this->parse('$a = 1; $b = 2; $c = $a <=> $b;')
            ->assertVariableSame('$a', $a = 1)
            ->assertVariableSame('$b', $b = 2)
            ->assertVariableSame('$c', $a <=> $b);
    }

    /**
     * Test Ternary Operator
     */
    public function testTernaryOperator()
    {
        $this->parse('$a = true ? 2 : 3; $b = false ? 2 : 3;')
            ->assertVariableSame('$a', true ? 2 : 3)
            ->assertVariableSame('$b', false ? 2 : 3);
    }
}
