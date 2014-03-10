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
     * Maps a given object request to the relational equivalent.
     * 
     * @param Object\IRequest $Request The request
     * @return Relational\ExistsSelect The exists relational select
     */
    final protected function MapToExistsSelect(Object\IRequest $Request) {
        $this->VerifyEntityTypeIsMapped($Request->GetEntityType());
        
        $RelationalCriterion = $this->GetRelationalCriterion($Request->GetEntityType());
        $PropertyExpressionResolver = $this->GetPropertyExpressionResolver($RelationalCriterion);
        $this->MapCriterion($Request->GetCriterion(), $RelationalCriterion, $PropertyExpressionResolver);
        
        $Select = new Relational\ExistsSelect($RelationalCriterion);
        $this->MapRequestAggregates($Request, $Select, $PropertyExpressionResolver);
                
        return $Select;
    }
    
    /**
     * Maps a given entity request to the relational equivalent.
     * 
     * @param Object\IEntityRequest $EntityRequest The entity request
     * @return Relational\ResultSetSelect The equivalent relational select
     */
    final protected function MapEntityRequest(Object\IEntityRequest $EntityRequest) {
        $EntityRelationalMap = $this->VerifyEntityTypeIsMapped($EntityRequest->GetEntityType());
        
        $RelationalCriterion = $this->GetRelationalCriterion($EntityRequest->GetEntityType());
        $PropertyExpressionResolver = $this->GetPropertyExpressionResolver($RelationalCriterion);
        $this->MapCriterion($EntityRequest->GetCriterion(), $RelationalCriterion, $PropertyExpressionResolver);
        
        $Select = new Relational\ResultSetSelect($RelationalCriterion);
        $this->MapRequestAggregates($EntityRequest, $Select, $PropertyExpressionResolver);
        $EntityRelationalMap->MapPropetiesToSelect($Select, $EntityRequest->GetProperties());
                
        return $Select;
    }
    
    /**
     * Maps a given data request to the relational equivalent.
     * 
     * @param Object\IDataRequest $DataRequest The data request
     * @return Relational\DataSelect The data select
     */
    final protected function MapDataRequest(Object\IDataRequest $DataRequest) {
        $this->VerifyEntityTypeIsMapped($DataRequest->GetEntityType());
        
        $RelationalCriterion = $this->GetRelationalCriterion($DataRequest->GetEntityType());
        $PropertyExpressionResolver = $this->GetPropertyExpressionResolver($RelationalCriterion);
        $this->MapCriterion($DataRequest->GetCriterion(), $RelationalCriterion, $PropertyExpressionResolver);
        
        $MappedAliasExpressionMap = $this->MapExpressions($DataRequest->GetAliasExpressionMap(), $PropertyExpressionResolver);
        
        $Select = new Relational\DataSelect($MappedAliasExpressionMap, $RelationalCriterion);
        $this->MapRequestAggregates($DataRequest, $Select, $PropertyExpressionResolver);
        
        return $Select;
    }
    
    private function MapRequestAggregates(Object\IRequest $Request, Relational\Select $Select, Expressions\PropertyExpressionResolver $PropertyExpressionResolver) {
        if ($Request->IsGrouped()) {
            foreach ($this->MapExpressions($Request->GetGroupByExpressions(), $PropertyExpressionResolver) as $GroupByExpression) {
                $Select->AddGroupByExpression($GroupByExpression);
            }
        }
        if ($Request->IsAggregateConstrained()) {
            foreach ($this->MapExpressions($Request->GetAggregatePredicateExpressions(), $PropertyExpressionResolver) as $AggregatePredicateExpression) {
                $Select->AddAggregatePredicateExpression($AggregatePredicateExpression);
            }
        }
    }
    
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Procedure mappers">
    
    /**
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
     * Maps the supplied object criterion the the relational equivalent.
     * 
     * @param Object\ICriterion $ObjectCriterion The object criterion to map
     * @param Relational\Criterion $RelationalCriterion The relational criterion to map to
     * @return Relational\Criterion
     */
    final protected function MapCriterion(
            Object\ICriterion $ObjectCriterion, 
            Relational\Criterion $RelationalCriterion = null,
            Expressions\PropertyExpressionResolver $PropertyExpressionResolver = null) {
        if($RelationalCriterion === null) {
            $RelationalCriterion = $this->GetRelationalCriterion($ObjectCriterion->GetEntityType());
        }
        if($PropertyExpressionResolver === null) {
            $PropertyExpressionResolver = $this->GetPropertyExpressionResolver($RelationalCriterion);
        }
        
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