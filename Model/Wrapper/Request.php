<?php
/**
 * Copyright © 2016 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\ShippingCore\Model\Wrapper;

class Request extends SourceWrapper
{

    /**
     * @return \Magento\Quote\Model\Quote\Address\RateRequest|null
     */
    protected function loadSource()
    {
        return $this->request;
    }
}
