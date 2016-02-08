<?php
/**
 * Copyright © 2016 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\ShippingCore\Model\Wrapper;

class ProductStockItem extends SourceWrapper
{

    /**
     * @var \Magento\CatalogInventory\Model\StockRegistry
     */
    protected $stockRegistry;

    /**
     * @param \Magento\CatalogInventory\Model\StockRegistry $stockRegistry
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @param \Owebia\ShippingCore\Helper\Registry $registry
     * @param mixed $data
     */
    public function __construct(
        \Magento\CatalogInventory\Model\StockRegistry $stockRegistry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Quote\Model\Quote\Address\RateRequest $request,
        \Owebia\ShippingCore\Helper\Registry $registry,
        $data = null
    ) {
        parent::__construct($objectManager, $request, $registry, $data);
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * @return \Magento\CatalogInventory\Model\Stock\Item
     */
    protected function loadSource()
    {
        return $this->stockRegistry
            ->getStockItem($this->data['product_id']);
    }
}
