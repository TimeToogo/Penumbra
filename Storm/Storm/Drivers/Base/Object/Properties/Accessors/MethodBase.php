<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

use \Storm\Core\Object\Expressions;

abstract class MethodBase extends ReflectionBase {
    protected $MethodName;
    protected $ConstantArguments;
    /**
     * @var \ReflectionMethod
     */
    protected $Reflection;
    
    public function __construct($MethodName, array $ConstantArguments = []) {
        $this->MethodName = $MethodName;
        $this->ConstantArguments = $ConstantArguments;
    }
    
    final public function GetMethodName() {
        return $this->MethodName;
    }
    
    public function Identifier(&$Identifier) {
        $Identifier .= sprintf('->%s(%s)',
                $this->MethodName,
                implode(', ', array_map(function ($I) { return var_export($I, true); }, $this->ConstantArguments)));
    }
    
    public function GetTraversalDepth() {
        return 1;
    }
    
    final protected function MatchesName(Expressions\Expression $Expression) {
        if(!($Expression instanceof Expressions\ValueExpression)) {
            return false;
        }
        
        return $Expression->GetValue() === $this->MethodName;
    }
    
    final protected function MatchesContantArguments(array $Expressions) {
        if(count($Expressions) !== count($this->ConstantArguments) || !$this->AreConstants($Expressions)) {
            return false;
        }
        
        return $this->Values($Expressions) === $this->ConstantArguments;
    }
    
    final protected function AreConstants(array $Expressions) {
        return count($Expressions) === 0 || count(array_filter($Expressions, function ($I) { return !($I instanceof Expressions\ValueExpression); })) > 0;
    }
    
    final protected function Values(array $Constants) {
        return array_map(function ($I) { return $I->GetValue(); }, $Constants);
    }
    
    final protected function Format($FunctionName, array $Arguments) {
        return sprintf('%s(%s)', 
                $FunctionName,
                implode(', ', 
                        array_map(
                            function ($Value) { 
                                return var_export($Value, true); 
                            },
                            $Arguments)));
    }
    
    public function __sleep() {
        return array_merge(parent::__sleep(), ['MethodName', 'ConstantArguments']);
    }
    
    protected function LoadReflection() {
        $Reflection = new \ReflectionMethod($this->EntityType, $this->MethodName);
        $Reflection->setAccessible(true);
        
        return $Reflection;
    }
}

?>