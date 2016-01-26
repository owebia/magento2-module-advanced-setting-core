<?php
/**
 * Copyright © 2016 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\ShippingCore\Helper;

use PhpParser\ParserFactory;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var array
     */
    protected $parsingCache = [];

    /**
     * @var array
     */
    protected $result = [];

    /**
     * @var boolean
     */
    protected $debug = true;

    /**
     * @var \Owebia\ShippingCore\Helper\Evaluator
     */
    protected $evaluator;

    /**
     * @var \Owebia\ShippingCore\Logger\Logger
     */
    protected $debugLogger;

    /**
     * @var \Owebia\ShippingCore\Helper\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Owebia\ShippingCore\Helper\Evaluator $evaluator
     * @param \Owebia\ShippingCore\Logger\Logger $debugLogger
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Owebia\ShippingCore\Helper\Evaluator $evaluator,
        \Owebia\ShippingCore\Logger\Logger $debugLogger
    ) {
        parent::__construct($context);
        $this->evaluator = $evaluator;
        $this->debugLogger = $debugLogger;
    }

    /**
     * @param string $configuration
     * @param \Owebia\ShippingCore\Helper\Registry $registry
     * @param boolean $debug
     * @return array
     */
    public function parse($configuration, \Owebia\ShippingCore\Helper\Registry $registry, $debug = false)
    {
        ini_set('xdebug.max_nesting_level', 3000);

        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP5);
        
        $hash = md5($configuration);
        if (!isset($this->parsingCache[$hash])) {
            // $stmts is an array of statement nodes
            $stmts = $parser->parse("<?php " . $configuration . ";");
            $this->parsingCache[$hash] = $stmts;
        } else {
            $stmts = $this->parsingCache[$hash];
        }
        
        $this->registry = $registry;
        $this->evaluator->reset();
        $this->evaluator->setRegistry($registry);
        $this->evaluator->setCallbackManager($this);

        $this->result = [];
        foreach ($stmts as $node) {
            $this->parseNode($node, $debug);
            $this->evaluator->reset();
        }
        
        return $this->result;
    }

    /**
     * @return \Owebia\ShippingCore\Model\Wrapper\ArrayWrapper
     * @throws \Exception
     */
    public function addMethod()
    {
        $args = func_get_args();
        if (count($args) != 2) {
            throw new \Exception("Invalid arguments count for addMethod FuncCall");
        }
        $methodId = array_shift($args);
        if (!is_string($methodId) || !preg_match('#^[a-z][a-z0-9_]*$#', $methodId)) {
            throw new \Exception("Invalid first argument for addMethod FuncCall: the first argument"
                . " must be a string and match the following pattern : ^[a-z][a-z0-9_]*$");
        }

        $methodOptions = array_shift($args);
        if (!is_array($methodOptions)) {
            throw new \Exception("Invalid second argument for addMethod FuncCall:"
                . " the second argument must be an array");
        }
        $this->result[$methodId] = (object) $methodOptions;
        return $this->registry->create('ArrayWrapper', [ 'data' => $methodOptions ]);
    }

    /**
     * @param object $node
     * @param bool $debug
     * @throws \Exception
     */
    protected function parseNode($node, $debug)
    {
        $methodId = null;
        try {
            $this->evaluator->evaluate($node);
            if ($debug) {
                $msg = $this->evaluator->getDebug();
                $this->addDebug($node, $msg, 'panel-info');
            }
        } catch (\Exception $e) {
            $error = (object) array(
                'error' => 'Invalid statement'
            );
            if (isset($methodId)) {
                $this->result[$methodId] = $error;
            } else {
                $this->result[] = $error;
            }
            if ($debug) {
                $msg = $this->evaluator->getDebug() . $e->getMessage();
                $this->addDebug($node, $msg, 'panel-danger');
            }
        }
    }

    /**
     * @param mixed $node
     * @param string $msg
     * @param string $panel
     */
    protected function addDebug($node, $msg, $panel = 'panel-default')
    {
        $title = $this->evaluator->prettyPrint($node);
        $this->debugLogger->collapse(
            "<pre class=php>" . htmlspecialchars($title) . "</pre>",
            $msg,
            $panel
        );
    }
}
