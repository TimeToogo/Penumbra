<?php

namespace Storm\Drivers\Intelligent\Object\Pinq;

use \Storm\Core\Object\Constraints;
use \Storm\Core\Object\EntityMap;
use \Storm\Drivers\Intelligent\Object\Pinq\Closure\Reader;
use \Storm\Drivers\Intelligent\Object\Code\Parsing\Parser;

class Predicate extends Constraints\Predicate {
    
    public function __construct(EntityMap $EntityMap, \Closure $ExpressionClosure) {
        parent::__construct($EntityMap->GetEntityType());
        
        $Parameters = $ExpressionParser->GetParameters();
        if(count($Parameters) === 0) {
            throw new \Exception;
        }
        
    }
    
    private function ParseClosure(\Closure $Closure) {
        $ClosureReader = new Reader($Closure);
        $Parameters = $ClosureReader->GetParameters();
        if(count($Parameters) !== 1) {
            throw new \Exception();
        }
        
        $PHPParser = new Parser();
        $BodyAST = $PHPParser->Parse($ClosureReader->GetBodySource());
        
        $EntityVariableName = $Parameters[0]->getName();
        $VariableMap = $ClosureReader->GetUsedVariablesMap();
        $RuleExpression = null;
        
        foreach ($BodyAST as $StatementNode) {
            if(!($StatementNode instanceof \PHPParser_Node_Stmt_Return)) {
                $this->ParseStatement($Node, $VariableMap);
            }
        }
        
    }
    
    private function ParseStatement(\PHPParser_Node $Node, array $ConstantVariables, array &$VariableMap) {
        switch (true) {
            case $Node instanceof \PHPParser_Node_Expr_Assign:
                $VariableNode = $Node->var;
                if(!($VariableNode instanceof \PHPParser_Node_Expr_Variable)) {
                    throw new \Exception();
                }
                $VariableMap[$VariableNode->name] = $VariableNode->expr;
                break;

            default:
                throw new \Exception();
        }
    }
}

?>