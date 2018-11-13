<?php
/**
 * Copyright © 2016-2018 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Model\Wrapper;

class Quote extends SourceWrapper
{
    /**
     * @return \Magento\Quote\Model\Quote|null
     */
    protected function loadSource()
    {
        if ($this->isBackendOrder()) { // For backend orders
            $session = $this->objectManager
                ->get(\Magento\Backend\Model\Session\Quote::class);
        } else {
            $session = $this->objectManager
                ->get(\Magento\Checkout\Model\Session::class);
        }

        return $session->getQuote();
    }
}
