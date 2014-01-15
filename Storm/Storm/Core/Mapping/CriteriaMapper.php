<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Core\Containers\Registrar;
use \Storm\Core\Containers\Map;

final class CriteriaMapper extends ObjectRelationalMapperBase {
    /**
     * @var ExpressionMapper 
     */
    private $ExpressionMapper;
    public function __construct(DomainDatabaseMap $DomainDatabaseMap, ExpressionMapper $ExpressionMapper) {
        parent::__construct($DomainDatabaseMap);
        
        $this->ExpressionMapper = $ExpressionMapper;
    }
    
    /**
     * @return ExpressionMapper
     */
    public function GetExpressionMapper() {
        return $this->ExpressionMapper;
    }

    public function MapObjectCriterion(Object\ICriterion $ObjectCriterion) {
        $EntityRelationalMap = $this->DomainDatabaseMap->GetRelationMap($ObjectCriterion->GetEntityType());
        
        $RelationalCriterion = $EntityRelationalMap->GetCriterion();
        $this->MapCriterion(
                $EntityRelationalMap, 
                $ObjectCriterion, 
                $RelationalCriterion);
        
        return $RelationalCriterion;
    }

    public function MapCriterion(IEntityRelationalMap $EntityRelationalMap,
            Object\ICriterion $ObjectCriterion, Relational\Criterion $RelationalCriterion) {
        
        if ($ObjectCriterion->IsConstrained()) {
            foreach ($this->ExpressionMapper->MapExpressions($EntityRelationalMap, $ObjectCriterion->GetPredicateExpressions()) as $PredicateExpression) {
                $RelationalCriterion->AddPredicateExpression($PredicateExpression);
            }
        }
        
        if ($ObjectCriterion->IsOrdered()) {
            $ExpressionAscendingMap = $ObjectCriterion->GetOrderByExpressionsAscendingMap();
            
            foreach ($ExpressionAscendingMap as $Expression) {
                $IsAscending = $ExpressionAscendingMap[$Expression];
                $Expressions = $this->ExpressionMapper->MapExpression($EntityRelationalMap, $Expression);
                foreach($Expressions as $Expression) {
                    $RelationalCriterion->AddOrderByExpression($Expression, $IsAscending);
                }
            }
        }
        
        if ($ObjectCriterion->IsGrouped()) {
            foreach ($this->ExpressionMapper->MapExpressions($EntityRelationalMap, $ObjectCriterion->GetGroupByExpressions()) as $GroupByExpression) {
                $RelationalCriterion->AddGroupByExpression($GroupByExpression);
            }
        }
        
        if ($ObjectCriterion->IsRanged()) {
            $RelationalCriterion->SetRangeOffset($ObjectCriterion->GetRangeOffset());
            $RelationalCriterion->SetRangeAmount($ObjectCriterion->GetRangeAmount());
        }
    }
        
}

?>