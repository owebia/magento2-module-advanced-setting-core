<?php
/**
 * Copyright © 2016 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\ShippingCore\Model\Wrapper;

class Variable extends SourceWrapper
{

    /**
     * @return \Magento\Variable\Model\Variable
     */
    protected function loadSource()
    {
        return $this->objectManager
            ->create('Magento\Variable\Model\Variable');
    }

    /**
     * {@inheritDoc}
     * @see \Owebia\ShippingCore\Model\Wrapper\AbstractWrapper::loadData()
     */
    protected function loadData($key)
    {
        $source = $this->getSource();
        $source->setStoreId($this->request->getData('store_id'));
        $variable = $source->loadByCode($key);
        if (!$variable) {
            return null;
        }
        return $variable;
    }

    /**
     * {@inheritDoc}
     * @see \Owebia\ShippingCore\Model\Wrapper\AbstractWrapper::getAdditionalData()
     */
    protected function getAdditionalData()
    {
        $data = parent::getAdditionalData();
        foreach ($this->getSource()->getCollection() as $variable) {
            $data[$variable->getCode()] = $variable;
        }
        return $data;
    }
}
