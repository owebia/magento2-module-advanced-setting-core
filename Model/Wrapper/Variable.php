<?php
/**
 * Copyright © 2016-2018 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Model\Wrapper;

class Variable extends SourceWrapper
{
    /**
     * @return \Magento\Framework\DataObject|null
     */
    protected function loadSource()
    {
        $source = $this->objectManager
            ->create(\Magento\Variable\Model\Variable::class);
        if (isset($this->data['code'])) {
            $source->setStoreId($this->getStoreId())
                ->loadByCode($this->data['code']);
        }

        return $source;
    }

    /**
     * {@inheritDoc}
     * @see \Owebia\AdvancedSettingCore\Model\Wrapper\AbstractWrapper::loadData()
     */
    protected function loadData($key)
    {
        if (isset($this->data['code'])) {
            return parent::loadData($key);
        }

        return $this->createWrapper(
            [ 'code' => $key ],
            static::class
        );
    }

    /**
     * {@inheritDoc}
     * @see \Owebia\AdvancedSettingCore\Model\Wrapper\AbstractWrapper::getAdditionalData()
     */
    protected function getAdditionalData()
    {
        $data = parent::getAdditionalData();
        if (!isset($this->data['code'])) {
            foreach ($this->getSource()->getCollection() as $variable) {
                $data[$variable->getCode()] = $variable;
            }
        }

        return $data;
    }
}
