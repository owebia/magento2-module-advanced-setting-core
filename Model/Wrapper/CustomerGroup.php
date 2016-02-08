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
     * @var \Magento\Customer\Model\GroupRegistry
     */
    protected $groupRegistry;

    /**
     * @param \Magento\Customer\Model\GroupRegistry $groupRegistry
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @param \Owebia\ShippingCore\Helper\Registry $registry
     * @param mixed $data
     */
    public function __construct(
        \Magento\Customer\Model\GroupRegistry $groupRegistry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Quote\Model\Quote\Address\RateRequest $request,
        \Owebia\ShippingCore\Helper\Registry $registry,
        $data = null
    ) {
        parent::__construct($objectManager, $request, $registry, $data);
        $this->groupRegistry = $groupRegistry;
    }

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
        return $this->groupRegistry
            ->retrieve($customerGroupId);
    }
}
