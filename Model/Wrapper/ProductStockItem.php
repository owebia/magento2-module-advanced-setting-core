<?php
/**
 * Copyright © 2016 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\ShippingCore\Model\Wrapper;

class ProductStockItem extends SourceWrapper
{

    /**
     * @return \Magento\CatalogInventory\Model\Stock\Item
     */
    protected function loadSource()
    {
        return $this->objectManager
            ->create('Magento\CatalogInventory\Model\Stock\Item')
            ->load($this->data['product_id']);
    }
}
