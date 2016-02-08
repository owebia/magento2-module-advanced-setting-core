<?php
/**
 * Copyright © 2016 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\ShippingCore\Model\Wrapper;

class Category extends SourceWrapper
{

    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $categoryRespository;

    /**
     * @param \Magento\Catalog\Model\CategoryRepository $categoryRespository
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @param \Owebia\ShippingCore\Helper\Registry $registry
     * @param mixed $data
     */
    public function __construct(
        \Magento\Catalog\Model\CategoryRepository $categoryRespository,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Quote\Model\Quote\Address\RateRequest $request,
        \Owebia\ShippingCore\Helper\Registry $registry,
        $data = null
    ) {
        parent::__construct($objectManager, $request, $registry, $data);
        $this->categoryRespository = $categoryRespository;
    }

    /**
     * @return \Magento\Catalog\Model\Category
     */
    protected function loadSource()
    {
        if ($this->data instanceof \Magento\Catalog\Model\Category) {
            return $this->data;
        }
        return $this->categoryRespository
            ->get($this->data['id']);
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
