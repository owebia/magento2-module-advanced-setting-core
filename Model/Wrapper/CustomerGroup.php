<?php
/**
 * Copyright © 2016-2018 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Model\Wrapper;

class CustomerGroup extends SourceWrapper
{

    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Owebia\AdvancedSettingCore\Helper\Registry $registry
     * @param mixed $data
     */
    public function __construct(
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Owebia\AdvancedSettingCore\Helper\Registry $registry,
        $data = null
    ) {
        parent::__construct($objectManager, $backendAuthSession, $registry, $data);
        $this->groupRepository = $groupRepository;
    }

    /**
     * @return \Magento\Framework\DataObject|null
     */
    protected function loadSource()
    {
        $quoteWrapper = $this->registry->get('quote');
        if (isset($quoteWrapper) && $quoteWrapper->getSource() instanceof \Magento\Quote\Model\Quote) {
            $quote = $quoteWrapper->getSource();
            $customerGroupId = $quote->getCustomerGroupId();
        } elseif ($this->isBackendOrder()) { // For backend orders
            $customerGroupId = $this->objectManager
                ->get(\Magento\Backend\Model\Session\Quote::class)
                ->getQuote()
                ->getCustomerGroupId();
        } else {
            $customerGroupId = $this->objectManager
                ->get(\Magento\Customer\Model\Session::class)
                ->getCustomerGroupId();
        }

        return $this->groupRepository
            ->getById($customerGroupId);
    }
}
