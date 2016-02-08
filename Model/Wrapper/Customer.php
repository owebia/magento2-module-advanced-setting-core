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
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @param \Owebia\ShippingCore\Helper\Registry $registry
     * @param mixed $data
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Quote\Model\Quote\Address\RateRequest $request,
        \Owebia\ShippingCore\Helper\Registry $registry,
        $data = null
    ) {
        parent::__construct($objectManager, $backendAuthSession, $request, $registry, $data);
        $this->customerRepository = $customerRepository;
    }

    /**
     * @return \Magento\Customer\Api\Data\CustomerInterface
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
        return $this->customerRepository
            ->getById($customerId);
    }
}
