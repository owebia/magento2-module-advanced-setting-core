<?php
/**
 * Copyright © 2016-2017 Owebia. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Owebia\AdvancedSettingCore\Helper;

/**
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class Evaluator extends \Magento\Framework\App\Helper\AbstractHelper
{

    const UNDEFINED_INDEX = 301;

    /**
     * @var boolean
     */
    protected $debug = false;

    /**
     * @var array
     */
    protected $debugOutput = [];

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var integer
     */
    protected $counter = 1;

    /**
     * @var \Owebia\AdvancedSettingCore\Helper\Registry
     */
    protected $allowedFunctions = [
        // Arrays
        'in_array',
        'count',
        'array_filter',
        'array_intersect',
        'array_map',
        'array_reduce',
        'array_sum',
        'array_unique',
        // Strings
        'explode',
        'implode',
        'substr',
        'preg_match',
        // Date
        'date',
        'strtotime',
        // Math
        'abs',
        'max',
        'min',
        'ceil',
        'floor',
        'range',
        'round',
    ];

    /**
     * @var \Owebia\AdvancedSettingCore\Helper\Registry
     */
    protected $registry = null;

    /**
     * @var \Owebia\AdvancedSettingCore\Model\CallbackHandler
     */
    protected $callbackHandler = null;

    /**
     * @var \PhpParser\PrettyPrinter\Standard
     */
    protected $prettyPrinter = null;

    /**
     * @return string
     */
    public function getDebugOutput()
    {
        return implode("\n", $this->debugOutput);
    }

    /**
     * @param boolean $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * @param string $msg
     * @param mixed $expr
     * @throws \Exception
     */
    protected function error($msg, $expr)
    {
        $trace = debug_backtrace(false);
        $this->errors[] = [
            'level' => 'ERROR',
            'msg' => $msg,
            // 'code' => $this->prettyPrint($expr),
            'expression' => $expr,
            'line' => $trace[0]['line']
        ];
        throw new \Magento\Framework\Exception\LocalizedException(__($msg));
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        $msg = [];
        foreach ($this->errors as $error) {
            $msg[] = $error['msg'];
        }
        return implode('<br/>', $msg);
    }

    public function initialize()
    {
        $this->debugOutput = [];
        $this->errors = [];
        $this->counter = 1;
    }

    /**
     * @param mixed $node
     * @param mixed $result
     * @return mixed
     */
    protected function debug($node, $result, $wrap = true)
    {
        if ($this->debug) {
            $right = $this->prettyPrint($result);
            $left = $this->prettyPrint($node);
            $uid = 'p' . uniqid();
            if ($left !== $right) {
                $this->debugOutput[] = '<div data-target="#' . $uid . '"><pre class=php>'
                        . htmlspecialchars($left)
                    . '</pre>'
                    . '<div class="hidden target" id="' . $uid . '"><pre class="php result">'
                        . htmlspecialchars("// Result\n$right")
                    . '</pre></div></div>';
            }
        }
        return $wrap ? $this->wrap($result) : $result;
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
     * @return \PhpParser\PrettyPrinter\Standard
     */
    public function getPrettyPrinter()
    {
        if (!isset($this->prettyPrinter)) {
            $this->prettyPrinter = new \PhpParser\PrettyPrinter\Standard([
                'shortArraySyntax' => true
            ]);
        }
        return $this->prettyPrinter;
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function prettyPrint($value)
    {
        if (!isset($value) || is_bool($value) || is_int($value) || is_string($value)) {
            return var_export($value, true);
        } elseif (is_float($value)) {
            return (string) $value;
        } elseif (is_array($value)) {
            foreach ($value as $item) {
                if (is_object($item) || is_array($item)) {
                    return 'array(size:' . count($value) . ')';
                }
            }
            // return $this->getPrettyPrinter()->pExpr_Array(new \PhpParser\Node\Expr\Array_($value));
            return var_export($value, true);
        } elseif (is_object($value)) {
            if ($value instanceof \PhpParser\Node) {
                if ($value->hasAttribute('comments')) {
                    $value->setAttribute('comments', []);
                }
                return rtrim($this->getPrettyPrinter()->prettyPrint([
                    $value
                ]), ';');
            } elseif ($value instanceof \Owebia\AdvancedSettingCore\Model\Wrapper\AbstractWrapper) {
                return (string) $value;
            } else {
                return "/** @var " . get_class($value) . " \$obj */ \$obj";
            }
        } else {
            return $value;
        }
    }

    /**
     * @param array $stmts
     * @return mixed
     */
    public function evaluateStmts($stmts)
    {
        foreach ($stmts as $stmt) {
            if ($stmt instanceof \PhpParser\Node\Stmt\Return_) {
                return $stmt;
            }

            $result = $this->evaluate($stmt);
            if (is_array($result) && $this->doesArrayContainOnly($result, \PhpParse\AbstractNode::class)) {
                $result = $this->evaluateStmts($result);
            }
            if ($result instanceof \PhpParser\Node\Stmt\Return_) {
                return $result;
            }
        }
        return null;
    }

    protected function doesArrayContainOnly($data, $className)
    {
        foreach ($data as $item) {
            if (!$item instanceof $className) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param \PhpParser\Node\Expr\Closure $expression
     * @return \Closure|null
     * @throws \Exception
     */
    protected function closure(\PhpParser\Node\Expr\Closure $expression)
    {
        if ($expression->static !== false) {
            return $this->error("Unsupported code - closure \$expression->static !== false", $expression);
        }
        if ($expression->byRef !== false) {
            return $this->error("Unsupported code - closure \$expression->byRef !== false", $expression);
        }

        $evaluator = $this;

        return function () use ($expression, $evaluator) {
            $args = func_get_args();
            $evaluator->registry->createScope();
            try {
                foreach ($expression->params as $param) {
                    $value = empty($args) ? $evaluator->evaluate($param) : array_shift($args);
                    $evaluator->registry->register($param->name, $this->wrap($value));
                }
                
                $result = $evaluator->evaluateStmts($expression->stmts);
                if ($result instanceof \PhpParser\Node\Stmt\Return_) {
                    $result = $evaluator->evaluate($result);
                }
            } catch (\Exception $e) {
                $evaluator->registry->deleteScope();
                throw $e;
            }
            $evaluator->registry->deleteScope();
            return $result;
        };
    }

    /**
     * @param \Owebia\AdvancedSettingCore\Helper\Registry $registry
     * @return \Owebia\AdvancedSettingCore\Helper\Evaluator
     */
    public function setRegistry(\Owebia\AdvancedSettingCore\Helper\Registry $registry)
    {
        $this->registry = $registry;
        return $this;
    }

    /**
     * @param \Owebia\AdvancedSettingCore\Model\CallbackHandler $callbackHandler
     * @return \Owebia\AdvancedSettingCore\Helper\Evaluator
     */
    public function setCallbackManager(\Owebia\AdvancedSettingCore\Model\CallbackHandler $callbackHandler)
    {
        $this->callbackHandler = $callbackHandler;
        return $this;
    }

    /**
     * @param mixed $expression
     * @return mixed
     * @throws \Exception
     */
    public function evaluate($expression)
    {
        return $this->evl($expression);
    }

    /**
     * @param mixed $expr
     * @return mixed
     * @throws \Exception
     */
    protected function evl($expr)
    {
        if (is_string($expr)) {
            return $expr;
        }
        if (is_array($expr)) {
            return $expr;
        }
        
        $className = get_class($expr);
        switch ($className) {
            case "PhpParser\\Node\\Scalar\\DNumber":
            case "PhpParser\\Node\\Scalar\\LNumber":
            case "PhpParser\\Node\\Scalar\\String_":
                return $this->debug($expr, $expr->value);
            
            // Arithmetic Operators
            case "PhpParser\\Node\\Expr\\UnaryMinus":
                return $this->debug($expr, - $this->evl($expr->expr));
            case "PhpParser\\Node\\Expr\\BinaryOp\\Plus":
                return $this->debug($expr, $this->evl($expr->left) + $this->evl($expr->right));
            case "PhpParser\\Node\\Expr\\BinaryOp\\Minus":
                return $this->debug($expr, $this->evl($expr->left) - $this->evl($expr->right));
            case "PhpParser\\Node\\Expr\\BinaryOp\\Mul":
                return $this->debug($expr, $this->evl($expr->left) * $this->evl($expr->right));
            case "PhpParser\\Node\\Expr\\BinaryOp\\Div":
                return $this->debug($expr, $this->evl($expr->left) / $this->evl($expr->right));
            case "PhpParser\\Node\\Expr\\BinaryOp\\Mod":
                return $this->debug($expr, $this->evl($expr->left) % $this->evl($expr->right));
            case "PhpParser\\Node\\Expr\\BinaryOp\\Pow": // Operator ** Introduced in PHP 5.6
                return $this->debug($expr, pow($this->evl($expr->left), $this->evl($expr->right)));
            
            // Bitwise Operators
            case "PhpParser\\Node\\Expr\\BinaryOp\\BitwiseAnd":
                return $this->debug($expr, $this->evl($expr->left) & $this->evl($expr->right));
            case "PhpParser\\Node\\Expr\\BinaryOp\\BitwiseOr":
                return $this->debug($expr, $this->evl($expr->left) | $this->evl($expr->right));
            case "PhpParser\\Node\\Expr\\BinaryOp\\BitwiseXor":
                return $this->debug($expr, $this->evl($expr->left) ^ $this->evl($expr->right));
            case "PhpParser\\Node\\Expr\\BitwiseNot":
                return $this->debug($expr, ~ $this->evl($expr->expr));
            case "PhpParser\\Node\\Expr\\BinaryOp\\ShiftLeft":
                return $this->debug($expr, $this->evl($expr->left) << $this->evl($expr->right));
            case "PhpParser\\Node\\Expr\\BinaryOp\\ShiftRight":
                return $this->debug($expr, $this->evl($expr->left) >> $this->evl($expr->right));
            
            // Comparison Operators
            case "PhpParser\\Node\\Expr\\BinaryOp\\Equal":
                return $this->debug($expr, $this->evl($expr->left) == $this->evl($expr->right));
            case "PhpParser\\Node\\Expr\\BinaryOp\\Identical":
                return $this->debug($expr, $this->evl($expr->left) === $this->evl($expr->right));
            case "PhpParser\\Node\\Expr\\BinaryOp\\NotEqual":
                return $this->debug($expr, $this->evl($expr->left) != $this->evl($expr->right));
            case "PhpParser\\Node\\Expr\\BinaryOp\\NotIdentical":
                return $this->debug($expr, $this->evl($expr->left) !== $this->evl($expr->right));
            case "PhpParser\\Node\\Expr\\BinaryOp\\Smaller":
                return $this->debug($expr, $this->evl($expr->left) < $this->evl($expr->right));
            case "PhpParser\\Node\\Expr\\BinaryOp\\Greater":
                return $this->debug($expr, $this->evl($expr->left) > $this->evl($expr->right));
            case "PhpParser\\Node\\Expr\\BinaryOp\\SmallerOrEqual":
                return $this->debug($expr, $this->evl($expr->left) <= $this->evl($expr->right));
            case "PhpParser\\Node\\Expr\\BinaryOp\\GreaterOrEqual":
                return $this->debug($expr, $this->evl($expr->left) >= $this->evl($expr->right));
            
            // Logical Operators
            case "PhpParser\\Node\\Expr\\BinaryOp\\LogicalAnd":
                return $this->debug($expr, $this->evl($expr->left) and $this->evl($expr->right));
            case "PhpParser\\Node\\Expr\\BinaryOp\\LogicalOr":
                return $this->debug($expr, $this->evl($expr->left) or $this->evl($expr->right));
            case "PhpParser\\Node\\Expr\\BinaryOp\\LogicalXor":
                return $this->debug($expr, $this->evl($expr->left) xor $this->evl($expr->right));
            case "PhpParser\\Node\\Expr\\BooleanNot":
                return $this->debug($expr, !$this->evl($expr->expr));
            case "PhpParser\\Node\\Expr\\BinaryOp\\BooleanAnd":
                return $this->debug($expr, $this->evl($expr->left) && $this->evl($expr->right));
            case "PhpParser\\Node\\Expr\\BinaryOp\\BooleanOr":
                return $this->debug($expr, $this->evl($expr->left) || $this->evl($expr->right));
            
            // Casting
            case "PhpParser\\Node\\Expr\\Cast\\String_":
                return $this->debug($expr, (string) $this->evl($expr->expr));
            case "PhpParser\\Node\\Expr\\Cast\\Int_":
                return $this->debug($expr, (int) $this->evl($expr->expr));
            case "PhpParser\\Node\\Expr\\Cast\\Bool_":
                return $this->debug($expr, (bool) $this->evl($expr->expr));
            case "PhpParser\\Node\\Expr\\Cast\\Double":
                return $this->debug($expr, (double) $this->evl($expr->expr));
            case "PhpParser\\Node\\Expr\\Cast\\Object_":
                return $this->debug($expr, (object) $this->evl($expr->expr));
            case "PhpParser\\Node\\Expr\\Cast\\Array_":
                return $this->debug($expr, (array) $this->evl($expr->expr));
            
            // String Operators
            case "PhpParser\\Node\\Expr\\BinaryOp\\Concat":
                return $this->debug($expr, $this->evl($expr->left) . $this->evl($expr->right));
            
            case "PhpParser\\Node\\Expr\\BinaryOp\\Coalesce": // Operator ?? Introduced in PHP 7
                try {
                    $left = $this->evl($expr->left);
                } catch (\OutOfBoundsException $e) {
                    $left = null;
                }
                return $this->debug($expr, null !== $left ? $left : $this->evl($expr->right));
            
            case "PhpParser\\Node\\Expr\\Ternary":
                return $this->debug($expr, $this->evl($expr->cond)
                    ? $this->evl($expr->if)
                    : $this->evl($expr->else));
            
            case "PhpParser\\Node\\Expr\\Isset_":
                try {
                    $result = $this->evl($expr->vars[0]);
                } catch (\OutOfBoundsException $e) {
                    $result = null;
                }
                return $this->debug($expr, $result  !== null);

            case "PhpParser\\Node\\Expr\\MethodCall":
                return $this->evaluateMethodCall($expr);

            case "PhpParser\\Node\\Expr\\ArrayDimFetch":
                $propertyName = $this->evl($expr->dim);
                $variable = $this->evl($expr->var);
                if ($variable instanceof \PhpParser\Node\Expr\ArrayItem) {
                    $variable = $this->evl($variable->value);
                }
                if ($variable instanceof \PhpParser\Node\Expr\Array_) {
                    $variable = $this->evl($variable);
                }
                // var_export($variable);
                if (!is_array($variable)) {
                    $variableName = isset($expr->var->name) ? $expr->var->name : '';
                    return $this->error("Unsupported ArrayDimFetch expression"
                        . " - Variable \${$variableName} is not an array", $expr);
                } elseif (is_array($variable) && isset($variable[$propertyName])) {
                    return $this->debug($expr, $variable[$propertyName]);
                } elseif (is_array($variable) && !isset($variable[$propertyName])) {
                    $this->debug($expr, null);
                    throw new \OutOfBoundsException("Undefined index: $propertyName", $this::UNDEFINED_INDEX);
                }
                return $this->error("Unsupported ArrayDimFetch expression", $expr);
            case "PhpParser\\Node\\Expr\\StaticPropertyFetch":
                $className = $this->evl($expr->class);
                if (true) { // StaticPropertyFetch is forbidden
                    return $this->error("Unsupported StaticPropertyFetch expression", $expr);
                }
                $propertyName = $this->evl($expr->name);
                $result = $this->registry->getGlobal($propertyName);
                return $this->debug($expr, $result);
            case "PhpParser\\Node\\Expr\\PropertyFetch":
                return $this->evaluatePropertyFetch($expr);
            case "PhpParser\\Node\\Expr\\Variable":
                return $this->debug($expr, $this->registry->get($expr->name));
            case "PhpParser\\Node\\Expr\\Array_":
                $items = [];
                foreach ($expr->items as $item) {
                    $value = $this->evl($item->value);
                    if (isset($item->key)) {
                        $items[$this->evl($item->key)] = $value;
                    } else {
                        $items[] = $value;
                    }
                }
                return $this->debug($expr, $items);
            case "PhpParser\\Node\\Expr\\ConstFetch":
                return $this->debug($expr, constant($expr->name->parts[0]));
            case "PhpParser\\Node\\Expr\\FuncCall":
                return $this->evaluateFuncCall($expr);
            case "PhpParser\\Node\\Expr\\Closure":
                return $this->debug($expr, $this->closure($expr));
            case "PhpParser\\Node\\Stmt\\Return_":
                return $this->debug($expr, $this->evl($expr->expr));
            case "PhpParser\\Node\\Stmt\\Global_":
                foreach ($expr->vars as $var) {
                    $variableName = $var->name;
                    $value = $this->registry->getGlobal($variableName);
                    $this->registry->declareGlobalAtCurrentScope($variableName);
                }
                return $this->debug($expr, null);
            case "PhpParser\\Node\\Expr\\Assign":
                if (!isset($expr->var->name)
                    || !isset($expr->expr)
                    || !($expr->var instanceof \PhpParser\Node\Expr\Variable)
                ) {
                    return $this->error("Unsupported Assign expression", $expr);
                }
                $variableName = $expr->var->name;
                $value = $this->evl($expr->expr);
                $this->registry->register($variableName, $value, true);
                return $this->debug($expr, $value);
            case "PhpParser\\Node\\Stmt\\If_":
                $cond = $this->evl($expr->cond);
                if ($cond) {
                    return $this->debug($expr, $this->evaluateStmts($expr->stmts), $wrap = false);
                }
                
                if (isset($expr->elseifs)) {
                    foreach ($expr->elseifs as $elseif) {
                        $cond = $this->evl($elseif->cond);
                        if ($cond) {
                            return $this->debug($expr, $this->evaluateStmts($elseif->stmts), $wrap = false);
                        }
                    }
                }
                if (isset($expr->else)) {
                    return $this->debug($expr, $this->evaluateStmts($expr->else->stmts), $wrap = false);
                }
                return $this->debug($expr, null);
            case "PhpParser\\Node\\Stmt\\Foreach_":
                $exp = $this->evl($expr->expr);
                $valueVar = $this->evl($expr->valueVar->name);
                $keyVar = $expr->keyVar ? $this->evl($expr->keyVar->name) : null;
                if (!is_array($exp)) {
                    return $this->error("Unsupported Foreach_ expression - Undefined variable", $expr);
                }
                foreach ($exp as $key => $value) {
                    $this->registry->register($valueVar, $this->wrap($value), true);
                    if ($keyVar) {
                        $this->registry->register($keyVar, $this->wrap($key), true);
                    }
                    $result = $this->evaluateStmts($expr->stmts);
                    if ($result instanceof \PhpParser\Node\Stmt\Return_) {
                        return $this->debug($expr, $result);
                    }
                }
                return $this->debug($expr, null);
            case "PhpParser\\Node\\Name":
                if (!isset($expr->parts) || count($expr->parts) != 1) {
                    return $this->error("Unsupported Name expression", $expr);
                }
                return $this->debug($expr, $expr->parts[0]);
            default:
                return $this->error("Unsupported expression {$className}", $expr);
        }
    }

    /**
     * @param type $expr
     * @return mixed
     */
    protected function evaluatePropertyFetch($expr)
    {
        $propertyName = $this->evl($expr->name);
        $variable = $this->evl($expr->var);
        if ($variable instanceof \PhpParser\Node\Expr\ArrayItem) {
            $variable = $this->evl($variable->value);
        }
        if ($variable instanceof \PhpParser\Node\Expr\Array_) {
            $variable = $this->evl($variable);
        }
        if (!isset($variable) && isset($expr->var->name) && is_string($expr->var->name)) {
            return $this->error("Unknown variable \${$expr->var->name} - " . get_class($variable), $expr);
        }
        
        if (is_array($variable) && isset($variable[$propertyName])) {
            return $this->debug($expr, $variable[$propertyName]);
        } elseif (is_object($variable)
            && $variable instanceof \Owebia\AdvancedSettingCore\Model\Wrapper\AbstractWrapper
        ) {
            return $this->debug($expr, $variable->$propertyName);
        } elseif (is_object($variable) && isset($variable->{$propertyName})) {
            return $this->debug($expr, $variable->{$propertyName});
        } elseif (is_object($variable)) {
            return $this->error("Unsupported PropertyFetch expression - " . get_class($variable), $expr);
        }
        return $this->error("Unsupported PropertyFetch expression", $expr);
    }

    /**
     * @param type $expr
     * @return mixed
     */
    protected function evaluateMethodCall($expr)
    {
        $methodName = $this->evl($expr->name);
        $variable = $this->evl($expr->var);
        if ($variable instanceof \PhpParser\Node\Expr\ArrayItem) {
            $variable = $this->evl($variable->value);
        }
        if ($variable instanceof \PhpParser\Node\Expr\Array_) {
            $variable = $this->evl($variable);
        }

        $method = null;
        $variableName = isset($expr->var->name) ? $expr->var->name : '';
        if (!isset($variable)) {
            return $this->error("Unsupported MethodCall expression"
                . " - Unkown variable \${$variableName}", $expr);
        }
        if (is_object($variable) && isset($variable->{$methodName}) && is_callable($variable->{$methodName})) {
            $method = $variable->{$methodName};
        } elseif ($variable instanceof \Owebia\AdvancedSettingCore\Model\Wrapper\AbstractWrapper && is_callable([
            $variable,
            $methodName
        ])) {
            $method = [
                $variable,
                $methodName
            ];
        } elseif ($variable instanceof \Owebia\AdvancedSettingCore\Model\Wrapper\AbstractWrapper && is_callable([
            $variable->getSource(),
            $methodName
        ])) {
            $method = [
                $variable->getSource(),
                $methodName
            ];
        } elseif (is_array($variable) && isset($variable[$methodName]) && is_callable($variable[$methodName])) {
            $method = $variable[$methodName];
        }
        if (!$method) {
            return $this->error("Unsupported MethodCall expression - Unkown method" . ( is_callable([
                $variable,
                $methodName
            ]) ? '1' : '0'), $expr);
        }
        $args = $this->evaluateArgs($expr);
        $result = $this->callFunction($method, $args);
        $result = $this->wrap($result);
        return $this->debug($expr, $result);
    }

    /**
     * @param mixed $method
     * @param array $args
     * @return type
     */
    protected function callFunction($method, $args = [])
    {
        return call_user_func_array($method, $args);
    }

    /**
     * @param type $expr
     * @return type
     */
    protected function evaluateFuncCall($expr)
    {
        if (isset($expr->name->parts)) {
            if (count($expr->name->parts) != 1) {
                return $this->error("Unsupported FuncCall expression", $expr);
            }
            $functionName = $expr->name->parts[0];
            $map = [
                'help' => [ $this, 'fnHelp' ],
            ];
            $isFunctionAllowed = in_array($functionName, $this->allowedFunctions)
                || in_array($functionName, array_keys($map));
            if (method_exists($this->callbackHandler, $functionName . 'Callback')) {
                $functionName = [ $this->callbackHandler, $functionName . 'Callback' ];
            } else {
                if (!$isFunctionAllowed && function_exists($functionName)) {
                    return $this->error("Unauthorized function '{$functionName}'", $expr);
                } elseif (!$isFunctionAllowed) {
                    return $this->error("Unknown function '{$functionName}'", $expr);
                }
                if (isset($map[$functionName])) {
                    $functionName = $map[$functionName];
                }
            }
            $args = $this->evaluateArgs($expr);
            $result = $this->callFunction($functionName, $args);
            return $this->debug($expr, $result);
        } elseif ($expr->name instanceof \PhpParser\Node\Expr\Variable) {
            $variable = $this->registry->get($expr->name->name);
            if (!isset($variable)) {
                return $this->error("Unsupported FuncCall expression - Unkown function", $expr);
            }
            if (!is_callable($variable)) {
                return $this->error("Unsupported FuncCall expression - Variable is not a function", $expr);
            }
            $args = $this->evaluateArgs($expr);
            $result = $this->callFunction($variable, $args);
            return $this->debug($expr, $result);
        } else {
            return $this->error("Unsupported FuncCall expression", $expr);
        }
    }

    /**
     * @param type $expr
     * @return array
     */
    protected function evaluateArgs($expr)
    {
        $args = [];
        foreach ($expr->args as $arg) {
            $args[] = $this->evl($arg->value);
        }
        return $args;
    }
}
