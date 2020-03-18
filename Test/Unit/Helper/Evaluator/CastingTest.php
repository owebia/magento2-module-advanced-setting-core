<?php
/**
 * Copyright © 2020 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Test\Unit\Helper\Evaluator\Operators;

/**
 * Test Type Casting
 * https://www.php.net/manual/en/language.types.type-juggling.php#language.types.typecasting
 */
class CastingTest extends AbstractTest
{
    /**
     * Test Integer Casting
     */
    public function testIntegerCasting()
    {
        $this->parse('$a = (int) 3.2; $b = (int) "3";')
            ->assertVariableSame('$a', 3)
            ->assertVariableSame('$b', 3);
    }

    /**
     * Test Boolean Casting
     */
    public function testBooleanCasting()
    {
        $this->parse('$a = (bool) 10; $b = (bool) 0;')
            ->assertVariableSame('$a', true)
            ->assertVariableSame('$b', false);
    }

    /**
     * Test Float Casting
     */
    public function testFloatCasting()
    {
        $this->parse('$a = (float) "3.2"; $b = (double) "1.5";')
            ->assertVariableSame('$a', 3.2)
            ->assertVariableSame('$b', 1.5);
    }

    /**
     * Test String Casting
     */
    public function testStringCasting()
    {
        $this->parse('$a = (string) 3;')
            ->assertVariableSame('$a', '3');
    }

    /**
     * Test Array Casting
     */
    public function testArrayCasting()
    {
        $this->parse('$a = 3; $b = (array) $a; $c = $b[0];')
            ->assertVariableSame('$c', 3);
    }

    /**
     * Test Object Casting
     */
    public function testObjectCasting()
    {
        $this->parse('$a = [ "a" => 1, "b" => 2, "c" => 3 ]; $b = (object) $a; $b = $a->b;')
            ->assertVariableSame('$b', 2);
    }
}
