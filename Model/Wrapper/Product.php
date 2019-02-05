<?php
/**
 * Copyright © 2016-2019 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Model\Wrapper;

use Owebia\AdvancedSettingCore\Model\Wrapper;

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
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var array
     */
    protected $attributes = null;

    /**
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Owebia\AdvancedSettingCore\Helper\Registry $registry
     * @param mixed $data
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Owebia\AdvancedSettingCore\Helper\Registry $registry,
        $data = null
    ) {
        parent::__construct($objectManager, $backendAuthSession, $registry, $data);
        $this->productRepository = $productRepository;
    }

    /**
     * @return \Magento\Framework\DataObject|null
     */
    protected function loadSource()
    {
        if ($this->data instanceof \Magento\Catalog\Api\Data\ProductInterface) {
            return $this->data;
        }
        return $this->productRepository
            ->getById($this->data['id']);
    }

    /**
     * Load source model
     *
     * @return Wrapper\Product
     */
    public function load()
    {
        $this->source = $this->productRepository
            ->getById($this->entity_id);
        $this->cache->setData([]);
        return $this;
    }

    protected function loadIfRequired($attributeCode)
    {
        if (!isset($this->attributes)) {
            $source = $this->getSource();
            $this->attributes = $source->getAttributes();
        }
        // If attribute data is not loaded, load it
        if (isset($this->attributes[$attributeCode]) && !$this->getSource()->hasData($attributeCode)) {
            $this->load();
        }
        return $this;
    }

    /**
     * @return string | null
     */
    public function getAttributeText($attributeCode)
    {
        $this->loadIfRequired($attributeCode);
        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        $product = $this->getSource();
        return $this->wrap($product->getAttributeText($attributeCode));
    }

    /**
     * {@inheritDoc}
     * @see Wrapper\AbstractWrapper::loadData()
     */
    protected function loadData($key)
    {
        switch ($key) {
            case 'attribute_set':
                return $this->createWrapper(
                    [ 'id' => (int) $this->{'attribute_set_id'} ],
                    Wrapper\ProductAttributeSet::class
                );
            case 'stock_item':
                return $this->createWrapper(
                    [ 'product_id' => (int) $this->{'entity_id'} ],
                    Wrapper\ProductStockItem::class
                );
            case 'category_id':
                /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
                $product = $this->getSource();
                return $product->getCategoryId();
            case 'category':
                /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
                $product = $this->getSource();
                return $product->getCategory();
            case 'category_ids':
                /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
                $product = $this->getSource();
                return $this->getSource()->getCategoryIds();
            case 'categories':
                /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
                $product = $this->getSource();
                $categories = [];
                foreach ($product->getCategoryCollection() as $category) {
                    $categories[] = $category;
                }
                return $categories;
            default:
                $this->loadIfRequired($key);
                return parent::loadData($key);
        }
    }
}
