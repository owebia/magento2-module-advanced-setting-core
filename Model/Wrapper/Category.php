<?php
/**
 * Copyright © 2016-2020 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Model\Wrapper;

class Category extends SourceWrapper
{

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRespository;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\Framework\Escaper $escaper
     * @param \Owebia\AdvancedSettingCore\Helper\Registry $registry
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRespository
     * @param mixed $data
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Framework\Escaper $escaper,
        \Owebia\AdvancedSettingCore\Helper\Registry $registry,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRespository,
        $data = null
    ) {
        parent::__construct($objectManager, $backendAuthSession, $escaper, $registry, $data);
        $this->categoryRespository = $categoryRespository;
    }

    /**
     * @return \Magento\Framework\DataObject|null
     */
    protected function loadSource()
    {
        if ($this->data instanceof \Magento\Catalog\Api\Data\CategoryInterface) {
            return $this->data;
        }
        return $this->categoryRespository
            ->get($this->data['id']);
    }

    /**
     * Load source model
     *
     * @return \Owebia\AdvancedSettingCore\Model\Wrapper\Category
     */
    public function load()
    {
        $this->source = $this->categoryRespository
            ->get($this->entity_id);
        $this->cache->setData([]);
        return $this;
    }
}
