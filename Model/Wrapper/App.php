<?php
/**
 * Copyright © 2019-2020 Owebia. All rights reserved.
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
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\Framework\Escaper $escaper
     * @param \Owebia\AdvancedSettingCore\Helper\Registry $registry
     * @param \Magento\Framework\App\State $appState
     * @param mixed $data
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Framework\Escaper $escaper,
        \Owebia\AdvancedSettingCore\Helper\Registry $registry,
        \Magento\Framework\App\State $appState,
        $data = []
    ) {
        parent::__construct($objectManager, $backendAuthSession, $escaper, $registry, $data);
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
