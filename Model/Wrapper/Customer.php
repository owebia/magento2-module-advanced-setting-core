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
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @param \Owebia\ShippingCore\Helper\Registry $registry
     * @param mixed $data
     */
    public function __construct(
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Quote\Model\Quote\Address\RateRequest $request,
        \Owebia\ShippingCore\Helper\Registry $registry,
        $data = null
    ) {
        parent::__construct($objectManager, $backendAuthSession, $request, $registry, $data);
        $this->customerRegistry = $customerRegistry;
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    protected function loadSource()
    {
        if ($this->isBackendOrder()) { // For backend orders
            $customerId = $this->objectManager
                ->get('Magento\Backend\Model\Session\Quote')
                ->getQuote()
                ->getCustomerId();
        } else {
            $customerId = $this->objectManager
                ->get('Magento\Customer\Model\Session')
                ->getCustomerId();
        }
        return $this->customerRegistry
            ->retrieve($customerId);
    }
}
