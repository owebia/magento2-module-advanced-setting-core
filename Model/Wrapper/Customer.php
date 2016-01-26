<?php
/**
 * Copyright © 2016 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\ShippingCore\Model\Wrapper;

class Customer extends SourceWrapper
{

    /**
     * @var array
     */
    protected $aliasMap = array(
        'id' => 'entity_id'
    );

    /**
     * @return \Magento\Customer\Model\Customer
     */
    protected function loadSource()
    {
        $customerId = $this->objectManager->get('Magento\Customer\Model\Session')->getCustomerId();
        if ($customerId == 0) { // For admin orders
            $customerId2 = $this->objectManager->get('Magento\Backend\Model\Session\Quote')
                ->getQuote()
                ->getCustomerId();
            if (isset($customerId2)) {
                $customerId = $customerId2;
            }
        }
        return $this->objectManager->create('Magento\Customer\Model\Customer')->load($customerId);
    }
}
