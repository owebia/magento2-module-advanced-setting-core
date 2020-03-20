<?php
/**
 * Copyright © 2016-2020 Owebia. All rights reserved.
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
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\Framework\Escaper $escaper
     * @param \Owebia\AdvancedSettingCore\Helper\Registry $registry
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param mixed $data
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Framework\Escaper $escaper,
        \Owebia\AdvancedSettingCore\Helper\Registry $registry,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        $data = null
    ) {
        parent::__construct($objectManager, $backendAuthSession, $escaper, $registry, $data);
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
