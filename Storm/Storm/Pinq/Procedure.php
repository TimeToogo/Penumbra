<?php

namespace Storm\Pinq;

use \Storm\Core\Object;
use \Storm\Core\Object\IEntityMap;
use \Storm\Api\Base\Repository;
use \Storm\Drivers\Base\Object\Procedure;
use \Storm\Drivers\Base\Object\DataRequest;
use \Storm\Core\Object\Expressions as O;
use \Storm\Core\Object\Expressions\Aggregates as A;

class Procedure extends Criteria  {
    /**
     * @var Repository
     */
    private $Repository;
    
    private $ExecutionFunctions = [];
    
    public function __construct(
            Repository $Repository,
            IEntityMap $EntityMap, 
            IFunctionToExpressionTreeConverter $FunctionToExpressionTreeConverter) {
        parent::__construct($EntityMap, $FunctionToExpressionTreeConverter);
        
        $this->Repository = $Repository;
    }
    
    /**
     * @return static
     */
    public function ClearQuery() {
        parent::ClearQuery();
        $this->ExecutionFunctions = [];
    }
    
    private function BuildProcedure() {
        return new Procedure(
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
        
        $this->Repository->Execute($this->BuildProcedure());
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
            if($Expression instanceof O\AssignmentExpression
                    && $Expression->GetAssignToExpression() instanceof O\PropertyExpression) {
                $AssignmentExpressions[] = $Expressions[$Key];
            }
            else if($Expression instanceof O\ReturnExpression) {
                break;
            }
        }
    }
}

?>