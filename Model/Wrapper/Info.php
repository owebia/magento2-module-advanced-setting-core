<?php
/**
 * Copyright © 2016-2020 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Model\Wrapper;

class Info extends ArrayWrapper
{
    /**
     * @var array
     */
    protected $additionalAttributes = [ 'memory_limit', 'memory_usage' ];

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\HTTP\PhpEnvironment\Request $request
     * @param \Owebia\AdvancedSettingCore\Helper\Registry $registry
     * @param string $carrierCode
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\HTTP\PhpEnvironment\Request $request,
        \Owebia\AdvancedSettingCore\Helper\Registry $registry,
        $carrierCode = null
    ) {
        parent::__construct($objectManager, $backendAuthSession, $escaper, $registry, [
            'server_os'       => PHP_OS,
            'server_software' => $request->getServerValue('SERVER_SOFTWARE'),
            'php_version'     => PHP_VERSION,
            'carrier_code'    => $carrierCode
        ]);
    }

    /**
     * {@inheritDoc}
     * @see \Owebia\AdvancedSettingCore\Model\Wrapper\AbstractWrapper::loadData()
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
