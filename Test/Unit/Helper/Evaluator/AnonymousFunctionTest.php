<?php
/**
 * Copyright © 2020 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Test\Unit\Helper\Evaluator;

/**
 * Test Anonymous functions
 * https://www.php.net/manual/en/functions.anonymous.php
 */
class AnonymousFunctionTest extends AbstractTest
{
    /**
     * Test Anonymous functions
     */
    public function testAnonymousFunction()
    {
        $this->parse('$fn = function ($a, $b) { return $a + $b; }; $c = $fn(2, 3);')
            ->assertVariableSame('$c', 5);
    }

    /**
     * Test global variables
     * https://www.php.net/manual/en/language.variables.scope.php#language.variables.scope.global
     */
    public function testGlobalVariable()
    {
        $this->parse('$a = 2; $b = 2; $c = 2;
                $fn = function ($b) { global $a, $b; $b = 3; $c = 3; return $a + $b; };
                $d = $fn(3);
            ')
            ->assertVariableSame('$b', 3)
            ->assertVariableSame('$c', 2)
            ->assertVariableSame('$d', 5);
    }

    /**
     * Test inheriting variables from the parent scope
     * https://www.php.net/manual/en/functions.anonymous.php#example-167
     */
    public function testInheritingVariable()
    {
        $this->parse('$a = 2; $fn = function ($b) use ($a) { return $a + $b; }; $c = $fn(3);')
            ->assertVariableSame('$c', 5);
    }
}
