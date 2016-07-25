<?php
/**
 * Copyright © 2016 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Owebia\AdvancedSettingCore\Block\System\Config\Form\Field;

class AbstractField extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * Returns the translation of the input string
     *
     * @return string
     */
    protected function translate()
    {
        $args = func_get_args();
        $format = array_shift($args);
        return vsprintf($format, $args);
    }
}
