<?php
/**
 * Copyright © 2016 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\ShippingCore\Model\Wrapper;

class ProductAttributeSet extends SourceWrapper
{

    /**
     * @return \Magento\Eav\Model\Entity\Attribute\Set
     */
    protected function loadSource()
    {
        return $this->objectManager
            ->create('Magento\Eav\Model\Entity\Attribute\Set')
            ->load($this->data['id']);
    }
}
