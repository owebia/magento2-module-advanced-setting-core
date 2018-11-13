<?php
/**
 * Copyright © 2016-2018 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Model\Wrapper;

class Customer extends SourceWrapper
{

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Owebia\AdvancedSettingCore\Helper\Registry $registry
     * @param mixed $data
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Owebia\AdvancedSettingCore\Helper\Registry $registry,
        $data = null
    ) {
        parent::__construct($objectManager, $backendAuthSession, $registry, $data);
        $this->customerRepository = $customerRepository;
    }

    /**
     * @return \Magento\Framework\DataObject|null
     */
    protected function loadSource()
    {
        $quoteWrapper = $this->registry->get('quote');
        if (isset($quoteWrapper) && $quoteWrapper->getSource() instanceof \Magento\Quote\Model\Quote) {
            $quote = $quoteWrapper->getSource();
            if ($customer = $quote->getCustomer()) {
                return $customer;
            }
        }

        if ($this->isBackendOrder()) { // For backend orders
            $customerId = $this->objectManager
                ->get(\Magento\Backend\Model\Session\Quote::class)
                ->getQuote()
                ->getCustomerId();
        } else {
            $customerId = $this->objectManager
                ->get(\Magento\Customer\Model\Session::class)
                ->getCustomerId();
        }

        return !$customerId ? null : $this->customerRepository
            ->getById($customerId);
    }
}
