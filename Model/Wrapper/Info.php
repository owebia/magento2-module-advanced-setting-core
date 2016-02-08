<?php
/**
 * Copyright © 2016 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\ShippingCore\Model\Wrapper;

class Info extends ArrayWrapper
{
    /**
     * @var array
     */
    protected $additionalAttributes = [ 'memory_limit', 'memory_usage' ];

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @param \Owebia\ShippingCore\Helper\Registry $registry
     * @param string $carrierCode
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Quote\Model\Quote\Address\RateRequest $request,
        \Owebia\ShippingCore\Helper\Registry $registry,
        $carrierCode = null
    ) {
        parent::__construct($objectManager, $backendAuthSession, $request, $registry, [
            'server_os'       => PHP_OS,
            'server_software' => $_SERVER['SERVER_SOFTWARE'],
            'php_version'     => PHP_VERSION,
            'carrier_code'    => $carrierCode
        ]);
    }

    /**
     * {@inheritDoc}
     * @see \Owebia\ShippingCore\Model\Wrapper\AbstractWrapper::loadData()
     */
    protected function loadData($key)
    {
        switch ($key) {
            case 'memory_limit':
                return ini_get('memory_limit');
            case 'memory_usage':
                return memory_get_usage(true);
        }
        return parent::loadData($key);
    }
}
