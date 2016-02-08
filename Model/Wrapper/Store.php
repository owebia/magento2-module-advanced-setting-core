<?php
/**
 * Copyright © 2016 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\ShippingCore\Model\Wrapper;

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
     * @var \Magento\Store\Model\StoreRepository
     */
    protected $storeRespository;

    /**
     * @param \Magento\Store\Model\StoreRepository $storeRespository
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @param \Owebia\ShippingCore\Helper\Registry $registry
     * @param mixed $data
     */
    public function __construct(
        \Magento\Store\Model\StoreRepository $storeRespository,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Quote\Model\Quote\Address\RateRequest $request,
        \Owebia\ShippingCore\Helper\Registry $registry,
        $data = null
    ) {
        parent::__construct($objectManager, $backendAuthSession, $request, $registry, $data);
        $this->storeRespository = $storeRespository;
    }

    /**
     * @return \Magento\Store\Model\Store
     */
    protected function loadSource()
    {
        return $this->storeRespository
            ->getById($this->request->getData('store_id'));
    }

    /**
     * {@inheritDoc}
     * @see \Owebia\ShippingCore\Model\Wrapper\AbstractWrapper::loadData()
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
