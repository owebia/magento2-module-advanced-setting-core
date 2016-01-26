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
     * @return \Magento\Store\Model\Store
     */
    protected function loadSource()
    {
        return $this->objectManager
            ->get('Magento\Store\Model\StoreManagerInterface')
            ->getStore($this->request->getData('store_id'));
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
