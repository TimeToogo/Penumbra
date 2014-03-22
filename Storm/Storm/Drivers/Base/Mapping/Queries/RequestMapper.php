<?php

namespace Storm\Drivers\Base\Mapping\Queries;

use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Mapping\ExpressionMapper;

class RequestMapper extends CriteriaMapper {
    
    public function MapRequestToExistsSelect(
            Object\IRequest $Request, 
            Relational\ExistsSelect $ExistsSelect,
            ExpressionMapper $ExpressionMapper) {        
        $this->MapRequestToSelect($Request, $ExistsSelect, $ExpressionMapper);
        
        return $ExistsSelect;
    }
    
    public function MapEntityRequest(
            Object\IEntityRequest $EntityRequest,
            Relational\ResultSetSelect $ResultSetSelect,
            Mapping\IEntityRelationalMap $EntityRelationalMap,
            ExpressionMapper $ExpressionMapper) {        
        $this->MapRequestToSelect($EntityRequest, $ResultSetSelect, $ExpressionMapper);
        
        $EntityRelationalMap->MapPropertiesToSelect($ResultSetSelect, [], $EntityRequest->GetProperties());
        
        return $ResultSetSelect;
    }
    
    public function MapDataRequest(
            Object\IDataRequest $DataRequest, 
            Relational\DataSelect $DataSelect, 
            array &$AliasReturnTypes,
            ExpressionMapper $ExpressionMapper) {
        $this->MapRequestToSelect($DataRequest, $DataSelect, $ExpressionMapper);
        
        $RelationalAliasExpressionMap = $ExpressionMapper->MapAll($DataRequest->GetAliasExpressionMap(), $AliasReturnTypes);
        $DataSelect->AddAllDataExpressions($RelationalAliasExpressionMap);
        
        return $DataSelect;
    }
    
    private function MapRequestToSelect(
            Object\IRequest $Request, 
            Relational\Select $Select, 
            ExpressionMapper $ExpressionMapper) {
        
        $this->MapCriteria($Request->GetCriteria(), $Select->GetCriteria(), $ExpressionMapper);
        
        $this->MapRequestAggregates($Request, $Select, $ExpressionMapper);
        
        return $Select;
    }
    
    private function MapRequestAggregates(
            Object\IRequest $Request, 
            Relational\Select $Select, 
            ExpressionMapper $ExpressionMapper) {
        if ($Request->IsGrouped()) {
            foreach ($ExpressionMapper->MapAll($Request->GetGroupByExpressions()) as $GroupByExpression) {
                $Select->AddGroupByExpression($GroupByExpression);
            }
        }
        
        if ($Request->IsAggregateConstrained()) {
            foreach ($ExpressionMapper->MapAll($Request->GetAggregatePredicateExpressions()) as $AggregatePredicateExpression) {
                $Select->AddAggregatePredicateExpression($AggregatePredicateExpression);
            }
        }
    }
}

?>