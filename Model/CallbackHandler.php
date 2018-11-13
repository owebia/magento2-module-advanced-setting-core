<?php
/**
 * Copyright © 2016-2018 Owebia. All rights reserved.
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
