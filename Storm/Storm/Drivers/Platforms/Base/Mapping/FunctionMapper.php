<?php

namespace Storm\Drivers\Platforms\Base\Mapping;

use \Storm\Drivers\Base\Mapping\Expressions;
use \Storm\Core\Object\Expressions as O;
use \Storm\Drivers\Base\Relational\Expressions as R;

abstract class FunctionMapper implements Expressions\IFunctionMapper {
    private $MatchingFunctions;
    
    public function __construct() {
        $this->MatchingFunctions = $this->MatchingFunctions();
    }
    
    /**
     * @return array<string, string>
     */
    protected abstract function MatchingFunctions();
    
    final public function MapFunctionCall(
            O\Expression $NameExpression, 
            array $MappedArgumentExpressions,
            &$ReturnType) {
        if(!($NameExpression instanceof O\ValueExpression)) {
            throw new \Storm\Core\Mapping\MappingException(
                    'Cannot map function call: function name is not a resolved value');
        }
        $FunctionName = $NameExpression->GetValue();
        
        $ArgumentExpressions = array_values($ArgumentExpressions);
        
        //OVERRIDE
        if(strpos($FunctionName, '__') === 0) {
            return R\Expression::FunctionCall(substr($FunctionName, 2), $ArgumentExpressions);
        }
        
        $FunctionName = strtolower($FunctionName);
        
        if(isset($this->MatchingFunctions[$FunctionName])) {
            return R\Expression::FunctionCall($this->MatchingFunctions[$FunctionName], $ArgumentExpressions);
        }
        
        if(!method_exists($this, $FunctionName)) {
            throw new \Storm\Core\NotSupportedException(
                    '%s does not support function: %s',
                    get_class($this),
                    $FunctionName);
        }
        $MappedName = $FunctionName;
        $OverrideExpression = $this->$FunctionName($MappedName, $MappedArgumentExpressions, $ReturnType);
        
        return ($OverrideExpression === null) ?
                R\Expression::FunctionCall($MappedName, $MappedArgumentExpressions) : $OverrideExpression;
    }
    
    public function FunctionMappingExample(&$MappedName, array &$ArgumentExpressions) {
        //Blah Blah
    }
}

?>