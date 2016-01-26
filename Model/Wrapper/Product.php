<?php
/**
 * Copyright © 2016 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\ShippingCore\Model\Wrapper;

class Product extends SourceWrapper
{
    /**
     * @var array
     */
    protected $additionalAttributes = [
        'attribute_set', 'stock_item',
        'category_id', 'category', 'category_ids', 'categories',
    ];

    /**
     * @return \Magento\Catalog\Model\Product
     */
    protected function loadSource()
    {
        if ($this->data instanceof \Magento\Catalog\Model\Product) {
            return $this->data;
        }
        return $this->objectManager
            ->create('Magento\Catalog\Model\Product')
            ->load($this->data['id']);
    }

    /**
     * Load source model
     * 
     * @return \Owebia\ShippingCore\Model\Wrapper\Product
     */
    public function load()
    {
        $this->source = $this->objectManager
            ->create('Magento\Catalog\Model\Product')
            ->load($this->entity_id);
        $this->cache->setData([]);
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \Owebia\ShippingCore\Model\Wrapper\AbstractWrapper::loadData()
     */
    protected function loadData($key)
    {
        switch ($key) {
            case 'attribute_set':
                return $this->createWrapper([ 'id' => (int) $this->{'attribute_set_id'} ], 'ProductAttributeSet');
            case 'stock_item':
                return $this->createWrapper([ 'product_id' => (int) $this->{'entity_id'} ], 'ProductStockItem');
            case 'category_id':
                /** @var \Magento\Catalog\Model\Product $product */
                $product = $this->getSource();
                return $product->getCategoryId();
            case 'category':
                /** @var \Magento\Catalog\Model\Product $product */
                $product = $this->getSource();
                return $product->getCategory();
            case 'category_ids':
                /** @var \Magento\Catalog\Model\Product $product */
                $product = $this->getSource();
                return $this->getSource()->getCategoryIds();
            case 'categories':
                /** @var \Magento\Catalog\Model\Product $product */
                $product = $this->getSource();
                $categories = [];
                foreach ($product->getCategoryCollection() as $category) {
                    $categories[] = $category;
                }
                return $categories;
            default:
                return parent::loadData($key);
        }
    }
}
