<?php

namespace Storm\Drivers\Base\Relational\Expressions\Converters;

abstract class FunctionConverter implements IFunctionConverter {
    private $MatchingFunctions;
    
    public function __construct() {
        $this->MatchingFunctions = $this->MatchingFunctions();
    }
    
    protected abstract function MatchingFunctions();
    
    final public function MapFunctionCallExpression($FunctionName, array $ArgumentExpressions = []) {
        $ArgumentExpressions = array_values($ArgumentExpressions);
        if(strpos($FunctionName, '__') === 0) {
            return $this->FunctionCall(substr($FunctionName, 2), $ArgumentExpressions);
        }
        
        $FunctionName = strtolower($FunctionName);
        
        if(isset($this->MatchingFunctions[$FunctionName])) {
            return $this->FunctionCall($this->MatchingFunctions[$FunctionName], $ArgumentExpressions);
        }
        
        if(!method_exists($this, $FunctionName)) {
            throw new \Storm\Core\NotSupportedException(
                    '%s does not support function: %s',
                    get_class($this),
                    $FunctionName);
        }
        $MappedName = $FunctionName;
        $OverrideExpression = $this->$FunctionName($MappedName, $ArgumentExpressions);
        
        return ($OverrideExpression === null) ?
                $this->FunctionCall($MappedName, $ArgumentExpressions) : $OverrideExpression;
    }
    
    final protected function FunctionCall($MappedName, array $ArgumentExpressions = []) {
        return new FunctionCallExpression($MappedName, new ValueListExpression($ArgumentExpressions));
    }
    
    public function FunctionMappingExample(&$MappedName, array &$ArgumentExpressions) {
        //Blah Blah
    }
}

?>