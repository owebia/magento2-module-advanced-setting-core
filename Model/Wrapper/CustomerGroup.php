<?php
/**
 * Copyright © 2016-2017 Owebia. All rights reserved.
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
     * @return \Magento\Customer\Api\Data\GroupInterface
     */
    protected function loadSource()
    {
        if ($this->isBackendOrder()) { // For backend orders
            $customerGroupId = $this->objectManager
                ->get('Magento\Backend\Model\Session\Quote')
                ->getQuote()
                ->getCustomerGroupId();
        } else {
            $customerGroupId = $this->objectManager
                ->get('Magento\Customer\Model\Session')
                ->getCustomerGroupId();
        }
        return $this->groupRepository
            ->getById($customerGroupId);
    }
}
