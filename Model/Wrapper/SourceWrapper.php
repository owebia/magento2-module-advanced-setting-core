<?php
/**
 * Copyright © 2016-2018 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Model\Wrapper;

class SourceWrapper extends AbstractWrapper
{

    /**
     * @var \Magento\Framework\DataObject | boolean
     */
    protected $source = false;

    /**
     * @return \Magento\Framework\DataObject|null
     */
    protected function loadSource()
    {
        return $this->data;
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getSource()
    {
        if ($this->source === false) {
            $this->source = $this->loadSource();
        }
        return $this->source;
    }

    /**
     * {@inheritDoc}
     * @see \Owebia\AdvancedSettingCore\Model\Wrapper\AbstractWrapper::getKeys()
     */
    protected function getKeys()
    {
        $source = $this->getSource();
        if ($source instanceof \Magento\Framework\DataObject) {
            return array_keys($source->getData());
        } elseif ($source instanceof \Magento\Framework\Api\AbstractSimpleObject) {
            // Not efficient but only for debug
            // see method _underscore in \Magento\Framework\DataObject
            return array_keys($source->__toArray());
        } else {
            return [];
        }
    }

    /**
     * {@inheritDoc}
     * @see \Owebia\AdvancedSettingCore\Model\Wrapper\AbstractWrapper::help()
     */
    public function help()
    {
        $source = $this->getSource();
        if ($source) {
            return parent::help();
        } else {
            $output = "Help on " . get_class($this) . " : No source defined";
        }
        return $output;
    }

    /**
     * {@inheritDoc}
     * @see \Owebia\AdvancedSettingCore\Model\Wrapper\AbstractWrapper::loadData()
     */
    protected function loadData($key)
    {
        $source = $this->getSource();
        if (!$source) {
            return null;
        }
        if ($source instanceof \Magento\Framework\DataObject) {
            return $source->getData($key);
        } elseif ($source instanceof \Magento\Framework\Api\AbstractSimpleObject) {
            $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            if (method_exists($source, $method)) {
                return $source->{$method}();
            }
        }
        return null;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        try {
            $className = get_class($this);
            $source = $this->getSource();
            $sourceClassName = null;
            if ($source && is_object($source)) {
                $sourceClassName = get_class($source);
            }
            $varName = lcfirst(($pos = strrpos($className, '\\')) ? substr($className, $pos + 1) : $className);
            $output = "/** @var \\$className \${$varName}"
                . (isset($sourceClassName) ? " (\\$sourceClassName)" : '')
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
