<?php
/**
 * Copyright © 2016-2018 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Model\Wrapper;

class Store extends SourceWrapper
{
    
    /**
     * @var array
     */
    protected $aliasMap = [
        'id' => 'store_id'
    ];

    /**
     * @var array
     */
    protected $additionalAttributes = [ 'name', 'address', 'phone' ];

    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $storeRespository;

    /**
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRespository
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Owebia\AdvancedSettingCore\Helper\Registry $registry
     * @param mixed $data
     */
    public function __construct(
        \Magento\Store\Api\StoreRepositoryInterface $storeRespository,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Owebia\AdvancedSettingCore\Helper\Registry $registry,
        $data = null
    ) {
        parent::__construct($objectManager, $backendAuthSession, $registry, $data);
        $this->storeRespository = $storeRespository;
    }

    /**
     * @return \Magento\Framework\DataObject|null
     */
    protected function loadSource()
    {
        return $this->storeRespository
            ->getById($this->getStoreId());
    }

    /**
     * {@inheritDoc}
     * @see \Owebia\AdvancedSettingCore\Model\Wrapper\AbstractWrapper::loadData()
     */
    protected function loadData($key)
    {
        switch ($key) {
            case 'name':
            case 'address':
            case 'phone':
                return $this->getSource()
                    ->getConfig('general/store_information/' . $key);
        }
        return parent::loadData($key);
    }
}
