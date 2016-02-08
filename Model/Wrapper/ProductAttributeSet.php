<?php
/**
 * Copyright © 2016 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\ShippingCore\Model\Wrapper;

class ProductAttributeSet extends SourceWrapper
{

    /**
     * @var \Magento\Eav\Api\AttributeSetRepositoryInterface
     */
    protected $attributeSetRespository;

    /**
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSetRespository
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @param \Owebia\ShippingCore\Helper\Registry $registry
     * @param mixed $data
     */
    public function __construct(
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSetRespository,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Quote\Model\Quote\Address\RateRequest $request,
        \Owebia\ShippingCore\Helper\Registry $registry,
        $data = null
    ) {
        parent::__construct($objectManager, $backendAuthSession, $request, $registry, $data);
        $this->attributeSetRespository = $attributeSetRespository;
    }

    /**
     * @return \Magento\Eav\Api\Data\AttributeSetInterface
     */
    protected function loadSource()
    {
        return $this->attributeSetRespository
            ->get($this->data['id']);
    }
}
