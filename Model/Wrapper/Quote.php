<?php
/**
 * Copyright © 2016 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\ShippingCore\Model\Wrapper;

class Quote extends SourceWrapper
{

    /**
     * @return \Magento\Quote\Model\Quote|null
     */
    protected function loadSource()
    {
        /** @var \Magento\Checkout\Model\Session $session */
        $session = $this->objectManager->get('Magento\Checkout\Model\Session');
        if (!$session->getQuoteId()) {
            return null; // Avoid infinite loop
        }
        return $session->getQuote();
    }
}
