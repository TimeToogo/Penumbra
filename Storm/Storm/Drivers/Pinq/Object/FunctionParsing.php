<?php

namespace Storm\Drivers\Pinq\Object;

use \Storm\Core\Object\IEntityMap;
use \Storm\Core\Object\Expressions as O;

trait FunctionParsing {
    
    /**
     * @var IEntityMap 
     */
    protected $EntityMap;
    
    /**
     * @var IFunctionToExpressionTreeConverter 
     */
    protected $FunctionToExpressionTreeConverter;
    
    final protected function ParseFunction(callable $Function, array $ParameterExpressionMap) {
        return $this->FunctionToExpressionTreeConverter->ConvertAndResolve($this->EntityMap, $Function, $ParameterExpressionMap);
    }
    
    final protected function ParseFunctionReturn(callable $Function, $Type, array $ParameterExpressionMap) {
        $ExpressionTree = $this->FunctionToExpressionTreeConverter->ConvertAndResolve($this->EntityMap, $Function, $ParameterExpressionMap);
        return $this->GetReturnExpression($ExpressionTree, $Type);
    }
    
    final protected function GetReturnExpression(Functional\ExpressionTree $ExpressionTree, $Type) {
        if(!$ExpressionTree->HasReturnExpression()) {
            throw FluentException::MustContainValidReturnExpression($Type);
        }
            
        $ReturnExpression = $ExpressionTree->GetReturnExpression();
        if(!$ReturnExpression->HasValueExpression()) {
            throw FluentException::MustContainValidReturnExpression($Type);
        }
        
        return $ReturnExpression->GetValueExpression();
    }
}

?>
