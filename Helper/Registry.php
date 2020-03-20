<?php
/**
 * Copyright © 2016-2020 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Helper;

use Owebia\AdvancedSettingCore\Model\Wrapper;

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
        $this->register(
            'app',
            $this->create(Wrapper\App::class)
        );
        $this->register(
            'request',
            $this->create(Wrapper\SourceWrapper::class, [ 'data' => $request ])
        );
        $this->register(
            'quote',
            $this->create(Wrapper\Quote::class)
        );
        $this->register(
            'customer',
            $this->create(Wrapper\Customer::class)
        );
        $this->register(
            'customer_group',
            $this->create(Wrapper\CustomerGroup::class)
        );
        $this->register(
            'variable',
            $this->create(Wrapper\Variable::class)
        );
        $this->register(
            'store',
            $this->create(Wrapper\Store::class)
        );
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
        return $this->wrapperFactory->create($className, $args);
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    public function wrap($data)
    {
        if (!isset($data)
            || is_bool($data)
            || is_int($data)
            || is_float($data)
            || is_string($data)
        ) {
            return $data;
        } elseif (is_array($data)) {
            return $data;
        } elseif (is_object($data)) {
            if ($data instanceof Wrapper\AbstractWrapper) {
                return $data;
            } elseif ($data instanceof \Closure) {
                return $data;
            } elseif ($data instanceof \Magento\Framework\Phrase) {
                return $data->__toString();
            } elseif ($data instanceof \Magento\Quote\Model\Quote\Item) {
                return $this->create(Wrapper\QuoteItem::class, [ 'data' => $data ]);
            } elseif ($data instanceof \Magento\Catalog\Api\Data\ProductInterface) {
                return $this->create(Wrapper\Product::class, [ 'data' => $data ]);
            } elseif ($data instanceof \Magento\Catalog\Api\Data\CategoryInterface) {
                return $this->create(Wrapper\Category::class, [ 'data' => $data ]);
            } else {
                return $this->create(Wrapper\SourceWrapper::class, [ 'data' => $data ]);
            }
        } else {
            // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
            throw new \Magento\Framework\Exception\LocalizedException(__("Unsupported type %1", gettype($data)));
        }
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
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
