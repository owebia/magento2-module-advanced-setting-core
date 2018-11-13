<?php
/**
 * Copyright © 2016-2017 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Model\Wrapper;

class ArrayWrapper extends AbstractWrapper implements \ArrayAccess
{
    /**
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return parent::__get($offset);
    }

    /**
     * @param type $offset
     * @param type $value
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function offsetSet($offset, $value)
    {
        throw new \Magento\Framework\Exception\LocalizedException(__("Wrapper can not be modified"));
    }

    /**
     * @param type $offset
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function offsetUnset($offset)
    {
        throw new \Magento\Framework\Exception\LocalizedException(__("Wrapper can not be modified"));
    }

    /**
     * {@inheritDoc}
     * @see \Owebia\AdvancedSettingCore\Model\Wrapper\AbstractWrapper::loadData()
     */
    protected function loadData($key)
    {
        return $this->data[$key];
    }

    /**
     * @return array
     */
    protected function getKeys()
    {
        return array_keys($this->data);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        try {
            $className = get_class($this);
            $varName = lcfirst(($pos = strrpos($className, '\\')) ? substr($className, $pos + 1) : $className);
            $output = "/** @var \\$className \${$varName}"
                . " */\n\${$varName} ";
            return $output . $this->help();
        } catch (\Exception $e) {
            if (isset($output)) {
                return $output . $e->getMessage();
            }
            return $e->getMessage();
        }
    }
}
