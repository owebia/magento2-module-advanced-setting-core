<?php
/**
 * Copyright © 2020 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Test\Unit\Helper\Evaluator;

class StmtIfTest extends AbstractTest
{
    /**
     * Test If
     */
    public function testIf()
    {
        $this->parse('$a = 1; $b = 1; if (false) { $a = 2; } if (true) { $b = 2; }')
            ->assertVariableSame('$a', 1)
            ->assertVariableSame('$b', 2);
    }

    /**
     * Test If/Else
     */
    public function testIfElse()
    {
        $this->parse('$a = 1; $b = 1; if (false) { $a = 2; } else { $b = 2; }')
            ->assertVariableSame('$a', 1)
            ->assertVariableSame('$b', 2);
    }

    /**
     * Test If/ElseIf/Else
     */
    public function testIfElseIfElse()
    {
        $this->parse('$a = 1; $b = 1; if (false) { $a = 2; } elseif (true) { $b = 2; } else { $b = 3; }')
            ->assertVariableSame('$a', 1)
            ->assertVariableSame('$b', 2);
    }
}
