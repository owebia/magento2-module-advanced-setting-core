<?php
/**
 * Copyright © 2016 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Owebia\ShippingCore\Block\System\Config\Form\Field;

class About extends AbstractField
{

    /**
     * Retrieve element HTML markup
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $version = Mage::getConfig()->getNode('modules/Owebia_ShippingFree/version');
        return $this->translate('Version: %s', $version);
    }
}
