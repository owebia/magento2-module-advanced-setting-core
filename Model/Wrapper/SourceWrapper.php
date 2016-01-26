<?php
/**
 * Copyright © 2016 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\ShippingCore\Model\Wrapper;

class SourceWrapper extends AbstractWrapper
{

    /**
     * @var \Magento\Framework\DataObject | boolean
     */
    protected $source = false;

    /**
     * @return \Magento\Framework\DataObject
     */
    protected function loadSource()
    {
        return $this->data;
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    protected function getSource()
    {
        if ($this->source === false) {
            $this->source = $this->loadSource();
        }
        return $this->source;
    }

    /**
     * {@inheritDoc}
     * @see \Owebia\ShippingCore\Model\Wrapper\AbstractWrapper::getKeys()
     */
    protected function getKeys()
    {
        $source = $this->getSource();
        if ($source) {
            return array_keys($source->getData());
        } else {
            return [];
        }
    }

    /**
     * {@inheritDoc}
     * @see \Owebia\ShippingCore\Model\Wrapper\AbstractWrapper::help()
     */
    public function help()
    {
        $source = $this->getSource();
        if ($source) {
            return parent::help();
        } else {
            $output = "Help on " . get_class($this) . " : No source defined";
            // $this->_logger->debugCollapse("Help on " . get_class($this) . " : No source defined", '');
        }
        return $output;
    }

    /**
     * {@inheritDoc}
     * @see \Owebia\ShippingCore\Model\Wrapper\AbstractWrapper::loadData()
     */
    protected function loadData($key)
    {
        $source = $this->getSource();
        if (!$source) {
            return null;
        }
        return $source->getData($key);
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
