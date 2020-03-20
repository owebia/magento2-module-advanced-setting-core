<?php
/**
 * Copyright © 2019-2020 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Model;

class CallbackHandlerExtension implements CallbackHandlerExtensionInterface
{
    /**
     * @var \Owebia\AdvancedSettingCore\Model\CallbackHandler
     */
    protected $callbackHandler;

    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (method_exists($this, $method)) {
            // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
            return call_user_func_array([ $this, $method ], $arguments);
        } else {
            throw new \BadMethodCallException("Method $method not found");
        }
    }

    /**
     * @param \Owebia\AdvancedSettingCore\Model\CallbackHandler $callbackHandler
     * @return $this
     */
    public function setCallbackHandler($callbackHandler)
    {
        $this->callbackHandler = $callbackHandler;
        return $this;
    }

    /**
     * @return \Owebia\AdvancedSettingCore\Helper\Registry
     */
    public function getRegistry()
    {
        return $this->callbackHandler->getRegistry();
    }
}
