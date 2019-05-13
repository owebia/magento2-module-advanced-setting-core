<?php
/**
 * Copyright © 2016-2019 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Model;

class CallbackHandler
{
    /**
     * @var \Owebia\AdvancedSettingCore\Helper\Registry
     */
    protected $registry;

    /**
     * @var \Owebia\AdvancedSettingCore\Model\CallbackHandlerExtensionInterface
     */
    protected $callbackHandlerExtension;

    /**
     * @param \Owebia\AdvancedSettingCore\Model\CallbackHandlerExtensionInterface $callbackHandlerExtension
     */
    public function __construct(
        \Owebia\AdvancedSettingCore\Model\CallbackHandlerExtensionInterface $callbackHandlerExtension
    ) {
        $this->callbackHandlerExtension = $callbackHandlerExtension;
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return $this->callbackHandlerExtension
            ->setCallbackHandler($this)
            ->__call($method, $arguments);
    }

    /**
     * @param string $callback
     * @return bool
     */
    public function hasCallback($callback)
    {
        return method_exists($this, $callback) || method_exists($this->callbackHandlerExtension, $callback);
    }

    /**
     * @return string
     */
    public function helpCallback()
    {
        return "The result of the help call is visible in the backoffice";
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function errorCallback($msg)
    {
        throw new \Magento\Framework\Exception\LocalizedException(__($msg));
    }

    /**
     * @return string
     */
    public function appendParsingError($msg)
    {
        return $msg;
    }

    /**
     * @param \Owebia\AdvancedSettingCore\Helper\Registry $registry
     */
    public function setRegistry($registry)
    {
        $this->registry = $registry;
    }

    /**
     * @return \Owebia\AdvancedSettingCore\Helper\Registry $registry
     */
    public function getRegistry()
    {
        return $this->registry;
    }
}
