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
    
    private function GetPropertyExpressionResolver(Relational\Query $Query) {
        return new Expressions\PropertyExpressionResolver($Query->GetResultSetSpecification(), $this);
    }
    
    // <editor-fold defaultstate="collapsed" desc="Request  mappers">
    
    /**
     * Maps a given object request to the relational equivalent.
     * 
     * @param Object\IRequest $Request The request
     * @return Relational\ExistsSelect The exists relational select
     */
    final protected function MapToExistsSelect(Object\IRequest $Request) {
        $ExistsSelect = new Relational\ExistsSelect(new Relational\ResultSetSpecification(
                $this->GetResultSetSources($Request), 
                $this->GetSelectCriteria($Request->GetEntityType())));
        
        $this->MapRequestToSelect($Request, $ExistsSelect);
        
        return $ExistsSelect;
    }
    
    /**
     * Maps a given entity request to the relational equivalent.
     * 
     * @param Object\IEntityRequest $EntityRequest The entity request
     * @return Relational\ResultSetSelect The equivalent relational select
     */
    final protected function MapEntityRequest(Object\IEntityRequest $EntityRequest) {
        $ResultSetSelect = new Relational\ResultSetSelect(new Relational\ResultSetSpecification(
                $this->GetResultSetSources($EntityRequest), 
                $this->GetSelectCriteria($EntityRequest->GetEntityType())));
        
        $this->MapRequestToSelect($EntityRequest, $ResultSetSelect);
        
        $EntityRelationalMap = $this->GetEntityRelationalMap($EntityRequest->GetEntityType());
        $EntityRelationalMap->MapPropertiesToSelect($ResultSetSelect, $EntityRequest->GetProperties());
        
        return $ResultSetSelect;
    }
    
    /**
     * Maps a given data request to the relational equivalent.
     * 
     * @param Object\IDataRequest $DataRequest The data request
     * @return Relational\DataSelect The data select
     */
    final protected function MapDataRequest(Object\IDataRequest $DataRequest) {
        $DataSelect = new Relational\DataSelect([], new Relational\ResultSetSpecification(
                $this->GetResultSetSources($DataRequest), 
                $this->GetSelectCriteria($DataRequest->GetEntityType())));
        
        $PropertyExpressionResolver = $this->GetPropertyExpressionResolver($DataSelect);
        $this->MapRequestToSelect($DataRequest, $DataSelect, $PropertyExpressionResolver);
        
        $RelationalAliasExpressionMap = $this->MapExpressions($DataRequest->GetAliasExpressionMap(), $PropertyExpressionResolver);
        $DataSelect->AddAllDataExpressions($RelationalAliasExpressionMap);
        
        return $DataSelect;
    }
    
    private function MapRequestToSelect(
            Object\IRequest $Request, 
            Relational\Select $Select, 
            Expressions\PropertyExpressionResolver $PropertyExpressionResolver = null) {
        
        $PropertyExpressionResolver = $PropertyExpressionResolver ?: $this->GetPropertyExpressionResolver($Select);
        $this->MapCriteria($Request->GetCriteria(), $Select->GetCriteria(), $PropertyExpressionResolver);
        
        $this->MapRequestAggregates($Request, $Select, $PropertyExpressionResolver);
        
        return $Select;
    }
    
    private function GetResultSetSources(Object\IRequest $Request) {
        if($Request->HasSubEntityRequest()) {
            $SubEntitySelect = $this->MapEntityRequest($Request->GetSubEntityRequest());
            return new Relational\ResultSetSources($SubEntitySelect);
        }
        else {
            return $this->GetSelectSources($Request->GetEntityType());
        }
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
        
        $PropertyExpressionResolver = $this->GetPropertyExpressionResolver($Update);
        
        foreach($AssignmentExpressions as $AssignmentExpression) {
            $AssignToExpression = $AssignmentExpression->GetAssignToExpression();
            
            if(!($AssignToExpression instanceof Object\Expressions\PropertyExpression)) {
                throw new Mapping\MappingException('Cannot map assignment expression: invalid assign to expression expecting %s, %s given',
                        Object\Expressions\PropertyExpression::GetType(),
                        $AssignToExpression->GetType());
            }
            
            $ColumnExpression = $PropertyExpressionResolver->MapProperty($AssignToExpression);
            $Column = $ColumnExpression->GetColumn();
            
            $MappedAssignmentExpression = $this->MapExpression($AssignmentExpression, $PropertyExpressionResolver);
            $MappedNewValueExpression = $this->Platform->GetOperationMapper()->MapAssignmentToBinary(
                    $ColumnExpression, 
                    $AssignmentExpression->GetOperator(), 
                    $MappedAssignmentExpression);
            
            $Update->AddColumn($Column, $MappedNewValueExpression);
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
        
    // <editor-fold defaultstate="collapsed" desc="Delete mappers">
    
    /**
     * Maps the supplied object criteria the the relational equivalent.
     * 
     * @param Object\ICriteria $ObjectCriteria The object criteria to map
     * @return Relational\Delete
     */
    final protected function MapCriteriaToDelete(Object\ICriteria $ObjectCriteria) {
        $EntityType = $ObjectCriteria->GetEntityType();
        $EntityRelationalMap = $this->VerifyEntityTypeIsMapped($EntityType);
        
        $Delete = new Relational\Delete(new Relational\ResultSetSpecification(
                $this->GetSelectSources($ObjectCriteria->GetEntityType()), 
                $this->GetSelectCriteria($ObjectCriteria->GetEntityType())));
        
        $Delete->AddTables($EntityRelationalMap->GetMappedPersistTables());
        $this->MapCriteria($ObjectCriteria, $Delete->GetCriteria());
        
        return $Delete;
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
        return $this->Platform->MapExpressions($Expressions, $PropertyExpressionResolver);
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