<?php
/**
 * Copyright © 2016-2020 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Model\Wrapper;

use Owebia\AdvancedSettingCore\Model\Wrapper;

abstract class AbstractWrapper
{

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $cache = null;

    /**
     * @var mixed
     */
    protected $data = null;

    /**
     * @var array
     */
    protected $aliasMap = [];

    /**
     * @var array
     */
    protected $additionalAttributes = [];

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $backendAuthSession;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \Owebia\AdvancedSettingCore\Helper\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\Framework\Escaper $escaper
     * @param \Owebia\AdvancedSettingCore\Helper\Registry $registry
     * @param mixed $data
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Framework\Escaper $escaper,
        \Owebia\AdvancedSettingCore\Helper\Registry $registry,
        $data = null
    ) {
        $this->objectManager = $objectManager;
        $this->backendAuthSession = $backendAuthSession;
        $this->escaper = $escaper;
        $this->registry = $registry;
        $this->logger = $this->objectManager->get(\Owebia\AdvancedSettingCore\Logger\Logger::class);
        $this->data = $data;
        $this->cache = $objectManager->create(\Magento\Framework\DataObject::class);
    }

    protected function isBackendOrder()
    {
        return $this->backendAuthSession->isLoggedIn();
    }

    /**
     * return array
     */
    protected function getAdditionalData()
    {
        $data = [];
        foreach ($this->additionalAttributes as $k) {
            $data[$k] = $this->{$k};
        }
        return $data;
    }

    /**
     * @param mixed $value
     * @param string|null $variableName
     * @return mixed
     */
    protected function convertToString($value, $variableName = null)
    {
        if (!isset($value)
            || is_bool($value)
            || is_float($value)
            || is_int($value)
            || is_string($value)
        ) {
            return var_export($value, true);
        } elseif (is_array($value)) {
            foreach ($value as $item) {
                if (is_object($item) || is_array($item)) {
                    return 'array(size:' . count($value) . ')';
                }
            }
            return var_export($value, true);
        } elseif (is_object($value)) {
            $variableName = isset($variableName) ? $variableName : 'obj';
            return "/** @var \\" . get_class($value) . " */ \$$variableName";
        } else {
            return $value;
        }
    }

    public function getStoreId()
    {
        return $this->registry->get('request')
            ->__get('store_id');
    }

    /**
     * @param mixed $data
     * @param string $className
     * @return \Owebia\AdvancedSettingCore\Model\Wrapper\AbstractWrapper
     */
    protected function createWrapper($data, $className = null)
    {
        return $this->registry->create($className ? $className : Wrapper\SourceWrapper::class, [ 'data' => $data ]);
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    protected function wrap($data)
    {
        return $this->registry->wrap($data);
    }

    /**
     * @return array
     */
    abstract protected function getKeys();

    /**
     * @param string $key
     * @return mixed
     */
    abstract protected function loadData($key);

    /**
     * @return string
     */
    protected function helpValue($value, $key)
    {
        $value = $this->escaper->escapeHtml(
            $this->convertToString($this->wrap($value), $key)
        );
        $value = str_replace("\n", "\n    ", $value);
        return "    " . $this->convertToString($key) . " => " . $value;
    }

    /**
     * @return string
     */
    public function help()
    {
        $output = " [\n";
        foreach ($this->getKeys() as $k) {
            $output .= $this->helpValue($this->{$k}, $k) . "\n";
        }
        if ($this->aliasMap) {
            $output .= "  // aliases\n";
            foreach ($this->aliasMap as $k => $originalKey) {
                $output .= $this->helpValue($this->{$k}, $k) . " // $originalKey\n";
            }
        }
        $additionalData = array_keys($this->getAdditionalData());
        if ($additionalData) {
            $output .= "  // additional attributes\n";
            foreach ($additionalData as $k) {
                $output .= $this->helpValue($this->{$k}, $k) . "\n";
            }
        }
        $output .= "]";
        return $output;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->aliasMap[$name])) {
            return $this->__get($this->aliasMap[$name]);
        }
        if (!$this->cache->hasData($name)) {
            $value = $this->wrap($this->loadData($name));
            $this->cache->setData($name, $value);
        }
        return $this->cache->getData($name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        $value = $this->__get($name);
        return $value !== null;
    }
}
