<?php
/**
 * Copyright © 2019 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Model\Wrapper;

class App extends ArrayWrapper
{
    /**
     * @var array
     */
    protected $additionalAttributes = [
        'area_code',
    ];

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Owebia\AdvancedSettingCore\Helper\Registry $registry
     * @param mixed $data
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Owebia\AdvancedSettingCore\Helper\Registry $registry,
        $data = []
    ) {
        parent::__construct($objectManager, $backendAuthSession, $registry, $data);
        $this->appState = $appState;
    }

    /**
     * {@inheritDoc}
     * @see Wrapper\AbstractWrapper::loadData()
     */
    protected function loadData($key)
    {
        switch ($key) {
            case 'area_code':
                return $this->getAreaCode();
            default:
                return parent::loadData($key);
        }
    }

    public function getAreaCode()
    {
        return $this->appState->getAreaCode();
    }

    public function isAdminArea()
    {
        return $this->getAreaCode() === \Magento\Framework\App\Area::AREA_ADMINHTML;
    }

    public function isFrontendArea()
    {
        return $this->getAreaCode() === \Magento\Framework\App\Area::AREA_WEBAPI_REST;
    }
}
