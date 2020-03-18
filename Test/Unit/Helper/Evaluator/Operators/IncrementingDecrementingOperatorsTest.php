<?php
/**
 * Copyright © 2020 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Test\Unit\Helper\Evaluator\Operators;

/**
 * Test Incrementing/Decrementing Operators
 * https://www.php.net/manual/en/language.operators.increment.php
 */
class IncrementingDecrementingOperators extends AbstractTest
{
    /**
     * Test Pre-increment
     */
    public function testPreIncrement()
    {
        $this->parse('$a = 7; $b = ++$a;')
            ->assertVariableSame('$a', ($a = 7) + 1)
            ->assertVariableSame('$b', $b = ++$a);
    }

    /**
     * Test Post-increment
     */
    public function testPostIncrement()
    {
        $this->parse('$a = 7; $b = $a++;')
            ->assertVariableSame('$a', ($a = 7) + 1)
            ->assertVariableSame('$b', $b = $a++);
    }

    /**
     * Test Pre-decrement
     */
    public function testPreDecrement()
    {
        $this->parse('$a = 7; $b = --$a;')
            ->assertVariableSame('$a', ($a = 7) - 1)
            ->assertVariableSame('$b', $b = --$a);
    }

    /**
     * Test Post-decrement
     */
    public function testPostDecrement()
    {
        $this->parse('$a = 7; $b = $a--;')
            ->assertVariableSame('$a', ($a = 7) - 1)
            ->assertVariableSame('$b', $b = $a--);
    }
}
