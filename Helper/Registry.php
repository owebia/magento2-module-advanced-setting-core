<?php
/**
 * Copyright © 2016-2018 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Helper;

class Registry extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Owebia\AdvancedSettingCore\Model\WrapperFactory
     */
    protected $wrapperFactory;

    /**
     * @var array
     */
    protected $data = [
        [] // Main Scope
    ];

    /**
     * @var array
     */
    protected $globalVariables = [
        [] // Main Scope
    ];

    /**
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Owebia\AdvancedSettingCore\Model\WrapperFactory $wrapperFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Owebia\AdvancedSettingCore\Model\WrapperFactory $wrapperFactory
    ) {
        $this->wrapperFactory = $wrapperFactory;
        parent::__construct($context);
    }

    /**
     * @return \Owebia\AdvancedSettingCore\Helper\Registry
     */
    public function init(\Magento\Framework\DataObject $request)
    {
        $this->data = [
            []
        ];
        $this->register('request', $this->create('SourceWrapper', [
            'data' => $request,
        ]));
        $this->register('quote', $this->create('Quote'));
        $this->register('customer', $this->create('Customer'));
        $this->register('customer_group', $this->create('CustomerGroup'));
        $this->register('variable', $this->create('Variable'));
        $this->register('store', $this->create('Store'));
        return $this;
    }

    /**
     * @param string $className
     * @param array $arguments
     * @return \Owebia\AdvancedSettingCore\Model\Wrapper\AbstractWrapper
     */
    public function create($className, array $arguments = [])
    {
        $args = array_merge([
            'registry' => $this,
        ], $arguments);
        if (strpos($className, "\\") === false) {
            $className = "Owebia\\AdvancedSettingCore\\Model\\Wrapper\\$className";
        }
        return $this->wrapperFactory->create($className, $args);
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    public function wrap($data)
    {
        $type = gettype($data);
        if ($type == 'NULL' || $type == 'boolean' || $type == 'integer' || $type == 'double' || $type == 'string') {
            return $data;
        } elseif ($type == 'array') {
            return $data;
        } elseif ($type == 'object') {
            if ($data instanceof \Owebia\AdvancedSettingCore\Model\Wrapper\AbstractWrapper) {
                return $data;
            } elseif ($data instanceof \Closure) {
                return $data;
            } elseif ($data instanceof \Magento\Quote\Model\Quote\Item) {
                return $this->create('QuoteItem', [ 'data' => $data ]);
            } elseif ($data instanceof \Magento\Catalog\Api\Data\ProductInterface) {
                return $this->create('Product', [ 'data' => $data ]);
            } elseif ($data instanceof \Magento\Catalog\Api\Data\CategoryInterface) {
                return $this->create('Category', [ 'data' => $data ]);
            } else {
                return $this->create('SourceWrapper', [ 'data' => $data ]);
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__("Unsupported type %s", $type));
        }
    }

    /**
     * @param string $name
     * @param int|null $scopeIndex
     * @return mixed
     */
    public function get($name, $scopeIndex = null)
    {
        if (! isset($scopeIndex)) {
            $scopeIndex = $this->getCurrentScopeIndex();
        }

        if (isset($this->globalVariables[$scopeIndex][$name])) {
            $scopeIndex = 0;
        }

        if (isset($this->data[$scopeIndex][$name])) {
            return $this->data[$scopeIndex][$name];
        }

        return null;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getGlobal($name)
    {
        return $this->get($name, 0);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param bool $override
     * @param int $scopeIndex
     */
    public function register($name, $value, $override = false, $scopeIndex = null)
    {
        if (! isset($scopeIndex)) {
            $scopeIndex = $this->getCurrentScopeIndex();
        }

        if (isset($this->globalVariables[$scopeIndex][$name])) {
            $scopeIndex = 0;
        }

        if (!$override && isset($this->data[$scopeIndex][$name])) {
            return;
        }

        $this->data[$scopeIndex][$name] = $value;
    }

    /**
     * @param string $name
     */
    public function declareGlobalAtCurrentScope($name)
    {
        $scopeIndex = $this->getCurrentScopeIndex();
        if (!isset($this->globalVariables[$scopeIndex][$name])) {
            $this->globalVariables[$scopeIndex][$name] = true;
        }
    }

    /**
     * @return int Current scope Index
     */
    public function getCurrentScopeIndex()
    {
        return count($this->data) - 1;
    }

    public function createScope()
    {
        $scopeIndex = $this->getCurrentScopeIndex() + 1;
        $this->data[$scopeIndex] = [];
        $this->globalVariables[$scopeIndex] = [];
    }

    public function deleteScope()
    {
        $scopeIndex = $this->getCurrentScopeIndex();
        unset($this->data[$scopeIndex]);
        unset($this->globalVariables[$scopeIndex]);
    }
}
