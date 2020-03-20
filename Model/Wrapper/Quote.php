<?php
/**
 * Copyright © 2016-2020 Owebia. All rights reserved.
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
        // Get quote from \Magento\Quote\Model\Quote\Address\RateRequest if possible
        $requestWrapper = $this->registry->get('request');
        if (isset($requestWrapper)
            && $requestWrapper->getSource() instanceof \Magento\Quote\Model\Quote\Address\RateRequest
        ) {
            $request = $requestWrapper->getSource();
            if ($items = $request->getAllItems()) {
                foreach ($items as $item) {
                    if ($quote = $item->getQuote()) {
                        return $quote;
                    }
                }
            }
        }

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
