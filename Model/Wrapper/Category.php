<?php
/**
 * Copyright © 2016 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\ShippingCore\Model\Wrapper;

class Category extends SourceWrapper
{
    /**
     * @return \Magento\Catalog\Model\Category
     */
    protected function loadSource()
    {
        if ($this->data instanceof \Magento\Catalog\Model\Category) {
            return $this->data;
        }
        return $this->objectManager
            ->create('Magento\Catalog\Model\Category')
            ->load($this->data['id']);
    }

    /**
     * Load source model
     * 
     * @return \Owebia\ShippingCore\Model\Wrapper\Category
     */
    public function load()
    {
        $this->source = $this->objectManager
            ->create('Magento\Catalog\Model\Category')
            ->load($this->entity_id);
        $this->cache->setData([]);
        return $this;
    }
}
