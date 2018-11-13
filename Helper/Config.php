<?php
/**
 * Copyright © 2016-2018 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Helper;

use PhpParser\ParserFactory;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var array
     */
    protected $parsingCache = [];

    /**
     * @var boolean
     */
    protected $debug = true;

    /**
     * @var \Owebia\AdvancedSettingCore\Helper\Evaluator
     */
    protected $evaluator;

    /**
     * @var \Owebia\AdvancedSettingCore\Logger\Logger
     */
    protected $debugLogger;

    /**
     * @var \Owebia\AdvancedSettingCore\Helper\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Owebia\AdvancedSettingCore\Helper\Evaluator $evaluator
     * @param \Owebia\AdvancedSettingCore\Logger\Logger $debugLogger
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Owebia\AdvancedSettingCore\Helper\Evaluator $evaluator,
        \Owebia\AdvancedSettingCore\Logger\Logger $debugLogger
    ) {
        parent::__construct($context);
        $this->evaluator = $evaluator;
        $this->debugLogger = $debugLogger;
    }

    /**
     * @param string $configuration
     * @param \Owebia\AdvancedSettingCore\Helper\Registry $registry
     * @param object $callbackManager
     * @param boolean $debug
     * @return Config
     */
    public function parse(
        $configuration,
        \Owebia\AdvancedSettingCore\Helper\Registry $registry,
        $callbackManager,
        $debug = false
    ) {
        $t0 = microtime(true);
        ini_set('xdebug.max_nesting_level', '3000');

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
        $this->evaluator->initialize();
        $this->evaluator->setDebug($debug);
        $this->evaluator->setRegistry($registry);
        $this->evaluator->setCallbackManager($callbackManager);

        foreach ($stmts as $node) {
            $this->parseNode($node, $callbackManager, $debug);
            $this->evaluator->initialize();
        }
        $t1 = microtime(true);
        if ($debug) {
            $this->debugLogger->debug("Duration " . round($t1 - $t0, 2) . " s");
        }
        return $this;
    }

    /**
     * @param object $node
     * @param bool $debug
     * @throws \Exception
     */
    protected function parseNode($node, $callbackManager, $debug)
    {
        try {
            $this->evaluator->evaluate($node);
            if ($debug) {
                $msg = $this->evaluator->getDebugOutput();
                $this->addDebug($node, $msg, 'panel-info');
            }
        } catch (\Exception $e) {
            $callbackManager->appendParsingError('Error ' . $e->getMessage());
            if ($debug) {
                $msg = $this->evaluator->getDebugOutput() . $e->getMessage();
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
