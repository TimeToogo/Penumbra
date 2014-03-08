<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

abstract class DomainDatabaseMap extends Mapping\DomainDatabaseMap {
    private $Platform;
    
    public function __construct(IPlatform $Platform) {
        parent::__construct();
        
        $this->Platform = $Platform;
        $this->GetDatabase()->SetPlatform($Platform->GetRelationalPlatform());
    }
    
    private function GetPropertyExpressionResolver(Relational\Criterion $Criterion) {
        return new Expressions\PropertyExpressionResolver($Criterion, $this);
    }
    
    // <editor-fold defaultstate="collapsed" desc="Request  mappers">
    
    /**
     * @access private
     * 
     * Maps a given object request to the relational equivalent.
     * 
     * @param IRequest $ObjectRequest The object request
     * @return Relational\Select The equivalent relational select
     */
    final protected function MapRequest(Object\IRequest $ObjectRequest) {
        $EntityRelationalMap = $this->VerifyEntityTypeIsMapped();
        
        $RelationalRequest = new Relational\Select($this->GetRelationalCriterion($ObjectRequest->GetEntityType()));
        $EntityRelationalMap->MapPropetiesToRelationalRequest($RelationalRequest, $ObjectRequest->GetProperties());
        
        $this->MapCriterion($ObjectRequest->GetCriterion(), $RelationalRequest->GetCriterion());
        
        return $RelationalRequest;
    }
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Procedure mappers">
    
    /**
     * @access private
     * 
     * Maps a supplied object procedure to an equivalent relational procedure.
     * 
     * @param Object\IProcedure $ObjectProcedure The object procedure
     * @return Relational\Update The equivalent relational update
     */
    final protected function MapProcedure(Object\IProcedure $ObjectProcedure) {
        $RelationalCriterion = $this->MapCriterion($ObjectProcedure->GetCriterion());
        $Update = new Relational\Update($RelationalCriterion);
        
        $AssignmentExpressions = $ObjectProcedure->GetExpressions();
        
        foreach($AssignmentExpressions as $AssignmentExpression) {
            $ResolvedExpressions = $this->GetPropertyMapping($AssignmentExpression->GetAssignToExpression()->GetProperty())->ResolveAssignmentExpression(
                    $AssignmentExpression->GetOperator(), 
                    $AssignmentExpression->GetAssignmentValueExpression());
            
            $Update->AddExpressions($ResolvedExpressions);
        }
        
        return $Update;
    }
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Criteria mappers">
    
    /**
     * @access private
     * 
     * Maps the supplied object criterion the the relational equivalent.
     * 
     * @param Object\ICriterion $ObjectCriterion The object criterion to map
     * @param Relational\Criterion $RelationalCriterion The relational criterion to map to
     * @return void
     */
    final protected function MapCriterion(Object\ICriterion $ObjectCriterion, Relational\Criterion $RelationalCriterion = null) {
        if($RelationalCriterion === null) {
            $RelationalCriterion = $this->GetRelationalCriterion($ObjectCriterion->GetEntityType());
        }
        
        $PropertyExpressionResolver = $this->GetPropertyExpressionResolver($Criterion);
        
        if ($ObjectCriterion->IsConstrained()) {
            foreach ($this->MapExpressions($ObjectCriterion->GetPredicateExpressions(), $PropertyExpressionResolver) as $PredicateExpression) {
                $RelationalCriterion->AddPredicateExpression($PredicateExpression);
            }
        }
        
        if ($ObjectCriterion->IsOrdered()) {
            $ExpressionAscendingMap = $ObjectCriterion->GetOrderByExpressionsAscendingMap();
            
            foreach ($ExpressionAscendingMap as $Expression) {
                $IsAscending = $ExpressionAscendingMap[$Expression];
                $Expressions = $this->MapExpression($Expression, $PropertyExpressionResolver);
                foreach($Expressions as $Expression) {
                    $RelationalCriterion->AddOrderByExpression($Expression, $IsAscending);
                }
            }
        }
        
        if ($ObjectCriterion->IsGrouped()) {
            foreach ($this->MapExpressions($ObjectCriterion->GetGroupByExpressions(), $PropertyExpressionResolver) as $GroupByExpression) {
                $RelationalCriterion->AddGroupByExpression($GroupByExpression);
            }
        }
        
        if ($ObjectCriterion->IsRanged()) {
            $RelationalCriterion->SetRangeOffset($ObjectCriterion->GetRangeOffset());
            $RelationalCriterion->SetRangeAmount($ObjectCriterion->GetRangeAmount());
        }
        
        return $RelationalCriterion;
    }
    
    // </editor-fold>
        
    // <editor-fold defaultstate="collapsed" desc="Expression mapping">
    
    /**
     * @access private
     * 
     * @param IEntityRelationalMap $EntityRelationalMap
     * @param Object\Expressions\Expression $Expressions
     * @return Relational\Expression[] The equivalent expressions
     */
    final protected function MapExpressions(array $Expressions, Expressions\PropertyExpressionResolver $PropertyExpressionResolver) {
        return $this->Platform->MapExpressions($Expression, $PropertyExpressionResolver);
    }

    /**
     * @access private
     * 
     * Maps the given object expression to the relational equivalent.
     * This will return an array as it is not a one-to-one mapping.
     * 
     * @return Relational\Expression The equivalent expression
     */
    final protected function MapExpression(Object\Expressions\Expression $Expression, Expressions\PropertyExpressionResolver $PropertyExpressionResolver) {
        return $this->Platform->MapExpression($Expression, $PropertyExpressionResolver);
    }
    
    // </editor-fold>
}

?>