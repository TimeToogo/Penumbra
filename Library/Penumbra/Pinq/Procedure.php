<?php

namespace Penumbra\Pinq;

use \Penumbra\Core\Object\IEntityMap;
use \Penumbra\Api\IEntityManager;
use \Penumbra\Drivers\Base\Object;
use \Penumbra\Core\Object\Expressions as O;

class Procedure extends Criteria  {
    /**
     * @var IEntityManager
     */
    private $EntityManager;
    
    private $ExecutionFunctions = [];
    
    public function __construct(
            IEntityManager $EntityManager,
            Functional\IFunctionToExpressionTreeConverter $FunctionToExpressionTreeConverter) {
        parent::__construct($EntityManager->GetEntityMap(), $FunctionToExpressionTreeConverter);
        
        $this->EntityManager = $EntityManager;
    }
    
    /**
     * @return static
     */
    public function ClearQuery() {
        parent::ClearQuery();
        $this->ExecutionFunctions = [];
    }
    
    private function BuildProcedure() {
        return new Object\Procedure(
                $this->EntityType, 
                $this->GetAssignmentExpressions($this->ExecutionFunctions), 
                $this->BuildCriteria());
    }
    
    /**
     * @return static
     */
    public function Run(callable $Function) {
        $this->ExecutionFunctions[] = $Function;
        return $this;
    }
    
    public function Execute(callable $Function = null) {
        if($Function !== null) {
            $this->ExecutionFunctions[] = $Function;
        }
        
        $this->EntityManager->Execute($this->BuildProcedure());
    }
    
    private function GetAssignmentExpressions(array $ExecutionFunctions) {
        $AssignmentExpressions = [];
        foreach($ExecutionFunctions as $ExecutionFunction) {
            $ExpressionTree = $this->GetEntityFunctionExpressionTree($ExecutionFunction);
            $this->AddPropertyAssignmentExpressions($AssignmentExpressions, $ExpressionTree);
        }
        
        return $AssignmentExpressions;
    }
    
    final protected function AddPropertyAssignmentExpressions(array &$AssignmentExpressions, Functional\ExpressionTree $ExpressionTree) {
        $Expressions = $ExpressionTree->GetExpressions();
        
        foreach ($Expressions as $Key => $Expression) {
            if($Expression instanceof O\ReturnExpression) {
                break;
            }
            
            $AssignmentExpression = $this->ParseAssignmentExpression($Expression);
            if($AssignmentExpression !== null) {
                $AssignmentExpressions[] = $AssignmentExpression;
            }
        }
    }
    
    protected function ParseAssignmentExpression(O\Expression $Expression) {
        switch ($Expression) {
            
            case $Expression instanceof O\AssignmentExpression 
                    && $Expression->GetAssignToExpression() instanceof O\PropertyExpression:
                return $Expression;
            
            case $Expression instanceof O\UnaryOperationExpression 
                    && $Expression->GetOperandExpression() instanceof O\PropertyExpression:
                
                switch ($Expression->GetOperator()) {
                    case O\Operators\Unary::Increment:
                    case O\Operators\Unary::PreIncrement:
                        return O\Expression::Assign(
                                $Expression->GetOperandExpression(), 
                                O\Operators\Assignment::Addition, 
                                O\Expression::Value(1));
                        
                    case O\Operators\Unary::Decrement:
                    case O\Operators\Unary::PreDecrement:
                        return O\Expression::Assign(
                                $Expression->GetOperandExpression(), 
                                O\Operators\Assignment::Subtraction,
                                O\Expression::Value(1));
                }
                
            default:
                return;
        }
    }
}

?>