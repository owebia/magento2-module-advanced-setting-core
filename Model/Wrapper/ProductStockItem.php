<?php
/**
 * Copyright © 2016-2020 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Model\Wrapper;

class ProductStockItem extends SourceWrapper
{

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\Framework\Escaper $escaper
     * @param \Owebia\AdvancedSettingCore\Helper\Registry $registry
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param mixed $data
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Framework\Escaper $escaper,
        \Owebia\AdvancedSettingCore\Helper\Registry $registry,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        $data = null
    ) {
        parent::__construct($objectManager, $backendAuthSession, $escaper, $registry, $data);
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * @return \Magento\Framework\DataObject|null
     */
    protected function loadSource()
    {
        return $this->stockRegistry
            ->getStockItem($this->data['product_id']);
    }
}
