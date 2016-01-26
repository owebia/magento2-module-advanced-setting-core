<?php
/**
 * Copyright © 2016 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\ShippingCore\Model\Wrapper;

class CustomerGroup extends SourceWrapper
{

    /**
     * @var array
     */
    protected $aliasMap = array(
        'id'   => 'customer_group_id',
        'code' => 'customer_group_code',
        'name' => 'customer_group_code'
    );

    /**
     * @return \Magento\Customer\Model\Group
     */
    protected function loadSource()
    {
        $customerGroupId = $this->objectManager->get('Magento\Customer\Model\Session')->getCustomerGroupId();
        if ($customerGroupId == 0) { // For admin orders
            $customerGroupId2 = $this->objectManager->get('Magento\Backend\Model\Session\Quote')
                ->getQuote()
                ->getCustomerGroupId();
            if (isset($customerGroupId2)) {
                $customerGroupId = $customerGroupId2;
            }
        }
        return $this->objectManager->create('Magento\Customer\Model\Group')->load($customerGroupId);
    }
}
