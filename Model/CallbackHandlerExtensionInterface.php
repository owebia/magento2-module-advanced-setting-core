<?php
/**
 * Copyright © 2019 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Model;

interface CallbackHandlerExtensionInterface
{
    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, $arguments);

    /**
     * @param \Owebia\AdvancedSettingCore\Model\CallbackHandler $callbackHandler
     * @return $this
     */
    public function setCallbackHandler($callbackHandler);
}
