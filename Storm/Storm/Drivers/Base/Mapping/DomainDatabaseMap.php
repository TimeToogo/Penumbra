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
    
    private function GetPropertyExpressionResolver(Relational\Criteria $Criteria) {
        return new Expressions\PropertyExpressionResolver($Criteria, $this);
    }
    
    // <editor-fold defaultstate="collapsed" desc="Request  mappers">
    
    /**
     * Maps a given object request to the relational equivalent.
     * 
     * @param Object\IRequest $Request The request
     * @return Relational\ExistsSelect The exists relational select
     */
    final protected function MapToExistsSelect(Object\IRequest $Request) {
        return $this->MapRequest($Request, 
                function ($Request, $SelectSources, $SelectCriteria) {
                    return new Relational\ExistsSelect($SelectSources, $SelectCriteria);
                });
    }
    
    /**
     * Maps a given entity request to the relational equivalent.
     * 
     * @param Object\IEntityRequest $EntityRequest The entity request
     * @return Relational\ResultSetSelect The equivalent relational select
     */
    final protected function MapEntityRequest(Object\IEntityRequest $EntityRequest) {
        return $this->MapRequest($EntityRequest, 
                function ($EntityRequest, $SelectSources, $SelectCriteria) {
                    $EntityRelationalMap = $this->GetEntityRelationalMap($EntityRequest->GetEntityType());
                    
                    $Select = new Relational\ResultSetSelect($SelectSources, $SelectCriteria);
                    $EntityRelationalMap->MapPropetiesToSelect($Select);
                    
                    return $Select;
                });
    }
    
    /**
     * Maps a given data request to the relational equivalent.
     * 
     * @param Object\IDataRequest $DataRequest The data request
     * @return Relational\DataSelect The data select
     */
    final protected function MapDataRequest(Object\IDataRequest $DataRequest) {
        return $this->MapRequest($DataRequest, 
                
                function ($DataRequest, $SelectSources, $SelectCriteria, $PropertyExpressionResolver) {
                    $MappedAliasExpressionMap = 
                            $this->MapExpressions($DataRequest->GetAliasExpressionMap(), $PropertyExpressionResolver);
                    
                    return new Relational\DataSelect($MappedAliasExpressionMap, $SelectSources, $SelectCriteria);
                });
    }
    
    private function MapRequest(Object\IRequest $Request, callable $SelectTypeFactory) {
        $EntityType = $Request->GetEntityType();
        $this->VerifyEntityTypeIsMapped($EntityType);
        
        $SelectCriteria = $this->GetSelectCriteria($EntityType);
        $SelectSources = $this->GetSelectSources($EntityType);
        $PropertyExpressionResolver = $this->GetPropertyExpressionResolver($SelectCriteria);
        $this->MapCriteria($Request->GetCriteria(), $SelectCriteria, $PropertyExpressionResolver);
        
        $Select = $SelectTypeFactory($Request, $SelectSources, $SelectCriteria, $PropertyExpressionResolver);
        
        $this->MapRequestAggregates($Request, $Select, $PropertyExpressionResolver);
        
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
        $RelationalCriteria = $this->MapCriteria($ObjectProcedure->GetCriteria());
        $Update = new Relational\Update($RelationalCriteria);
        
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
     * Maps the supplied object criteria the the relational equivalent.
     * 
     * @param Object\ICriteria $ObjectCriteria The object criteria to map
     * @param Relational\Criteria $RelationalCriteria The relational criteria to map to
     * @return Relational\Criteria
     */
    final protected function MapCriteria(
            Object\ICriteria $ObjectCriteria, 
            Relational\Criteria $RelationalCriteria = null,
            Expressions\PropertyExpressionResolver $PropertyExpressionResolver = null) {
        if($RelationalCriteria === null) {
            $RelationalCriteria = $this->GetSelectCriteria($ObjectCriteria->GetEntityType());
        }
        if($PropertyExpressionResolver === null) {
            $PropertyExpressionResolver = $this->GetPropertyExpressionResolver($RelationalCriteria);
        }
        
        if ($ObjectCriteria->IsConstrained()) {
            foreach ($this->MapExpressions($ObjectCriteria->GetPredicateExpressions(), $PropertyExpressionResolver) as $PredicateExpression) {
                $RelationalCriteria->AddPredicateExpression($PredicateExpression);
            }
        }
        
        if ($ObjectCriteria->IsOrdered()) {
            $ExpressionAscendingMap = $ObjectCriteria->GetOrderByExpressionsAscendingMap();
            
            foreach ($ExpressionAscendingMap as $Expression) {
                $IsAscending = $ExpressionAscendingMap[$Expression];
                $Expressions = $this->MapExpression($Expression, $PropertyExpressionResolver);
                foreach($Expressions as $Expression) {
                    $RelationalCriteria->AddOrderByExpression($Expression, $IsAscending);
                }
            }
        }
        
        if ($ObjectCriteria->IsRanged()) {
            $RelationalCriteria->SetRangeOffset($ObjectCriteria->GetRangeOffset());
            $RelationalCriteria->SetRangeAmount($ObjectCriteria->GetRangeAmount());
        }
        
        return $RelationalCriteria;
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