<?php
/**
 * Copyright © 2016 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\ShippingCore\Helper;

use Magento\Quote\Model\Quote\Address\RateRequest;

class Registry extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Owebia\ShippingCore\Model\WrapperFactory
     */
    protected $wrapperFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateRequest
     */
    protected $request;

    /**
     * @var array
     */
    protected $data = [
        [] // Main Scope
    ];

    /**
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Owebia\ShippingCore\Model\WrapperFactory $wrapperFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Owebia\ShippingCore\Model\WrapperFactory $wrapperFactory
    ) {
        $this->wrapperFactory = $wrapperFactory;
        parent::__construct($context);
    }

    /**
     *
     * @param \Owebia\ShippingFree\Model\Carrier $carrier
     * @param \Magento\Quote\Model\Quote\Address\RateRequest|null $request
     * @return \Owebia\ShippingCore\Helper\Registry
     */
    public function init(\Owebia\ShippingFree\Model\Carrier $carrier, RateRequest $request = null)
    {
        $this->request = $request;
        $this->data = [
            []
        ];
        $this->register('info', $this->create('Info', [
            'carrierCode' => isset($carrier) ? $carrier->getCarrierCode() : null
        ]));
        $this->register('quote', $this->create('Quote'));
        $this->register('customer', $this->create('Customer'));
        $this->register('customer_group', $this->create('CustomerGroup'));
        $this->register('variable', $this->create('Variable'));
        $this->register('store', $this->create('Store'));
        $this->register('request', $this->create('Request'));
        return $this;
    }

    /**
     * @param string $className
     * @param array $arguments
     * @return \Owebia\ShippingCore\Model\Wrapper\AbstractWrapper
     */
    public function create($className, array $arguments = array())
    {
        $args = array_merge([
            'registry' => $this,
            'request' => $this->request
        ], $arguments);
        if (strpos($className, "\\") === false) {
            $className = "Owebia\\ShippingCore\\Model\\Wrapper\\$className";
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
            if ($data instanceof \Owebia\ShippingCore\Model\Wrapper\AbstractWrapper) {
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
            throw new \Exception("Unsupported type $type");
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
            $scopeIndex = count($this->data) - 1;
        }
        // echo 'get ' . $name . ', ' . $scopeIndex . ' ';
        if (isset($this->data[$scopeIndex][$name])) {
            // echo "ok ;\n";
            return $this->data[$scopeIndex][$name];
        }
        // echo "ko ;\n";
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
     */
    public function register($name, $value, $override = false)
    {
        // echo 'register ' . $name . " ;\n";
        $lastScopeIndex = count($this->data) - 1;
        if (!$override && isset($this->data[$lastScopeIndex][$name])) {
            return;
        }
        $this->data[$lastScopeIndex][$name] = $value;
    }

    public function createScope()
    {
        $this->data[] = [];
    }

    public function deleteScope()
    {
        array_pop($this->data);
    }
}
