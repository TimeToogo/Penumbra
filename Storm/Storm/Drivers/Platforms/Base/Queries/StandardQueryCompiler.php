<?php

namespace Storm\Drivers\Platforms\Base\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\Expressions\Expression;

class StandardQueryCompiler implements Queries\IQueryCompiler {
    
    public function AppendSelect(QueryBuilder $QueryBuilder, Relational\Select $Select) {
        switch ($Select->GetSelectType()) {
            
            case Relational\SelectType::ResultSet:
                return $this->AppendResultSetSelect($QueryBuilder, $Select);
            
            case Relational\SelectType::Data:
                return $this->AppendDataSelect($QueryBuilder, $Select);
            
            case Relational\SelectType::Exists:
                return $this->AppendExistsSelect($QueryBuilder, $Select);
            
            default:
                throw new \Storm\Drivers\Base\Relational\PlatformException(
                        'Cannot compile select of type %s: unknown select type %s',
                        get_class($Select),
                        $Select->GetSelectType());
        }
    }
    
    protected function AppendResultSetSelect(QueryBuilder $QueryBuilder, Relational\ResultSetSelect $ResultSetSelect) {
        $QueryBuilder->Append('SELECT ');
        foreach($QueryBuilder->Delimit($ResultSetSelect->GetColumns(), ',') as $Column) {
            $QueryBuilder->AppendExpression($Column->GetReviveExpression(Expression::Column($Column)));
            $QueryBuilder->AppendIdentifier(' AS #', [$Column->GetIdentifier()]);
        }
        
        $this->AppendSelectClauses($QueryBuilder, $ResultSetSelect);
    }
    
    protected function AppendDataSelect(QueryBuilder $QueryBuilder, Relational\DataSelect $DataSelect) {
        $QueryBuilder->Append('SELECT ');
        foreach($QueryBuilder->Delimit($DataSelect->GetAliasExpressionMap(), ',') as $Alias => $Expression) {
            $QueryBuilder->AppendExpression($Expression);
            $QueryBuilder->AppendIdentifier(' AS #', $Alias);
        }
        
        $this->AppendSelectClauses($QueryBuilder, $DataSelect);
    }
    
    protected function AppendExistsSelect(QueryBuilder $QueryBuilder, Relational\ExistsSelect $ExistsSelect) {
        $QueryBuilder->Append('SELECT EXISTS (SELECT *');
        $this->AppendSelectClauses($QueryBuilder, $ExistsSelect);
        $QueryBuilder->Append(')');
    }
    
    protected function AppendSelectClauses(QueryBuilder $QueryBuilder, Relational\Select $Select) {
        $QueryBuilder->Append(' FROM ');
        $QueryBuilder->AppendTableDefinition($Select->GetCriteria());
        $this->AppendSelectCriteria($QueryBuilder, $Select);
    }
    
    protected function AppendSelectCriteria(QueryBuilder $QueryBuilder, Relational\Select $Select) {
        $Criteria = $Select->GetCriteria();
        
        $QueryBuilder->AppendWhere($Criteria);
        
        if($Select->IsGrouped()) {
            $this->AppendGroupByClause($QueryBuilder, $Select->GetGroupByExpressions());
        }
        if($Select->IsAggregateConstrained()) {
            $this->AppendHavingClause($QueryBuilder, $Select->GetAggregatePredicateExpressions());
        }
        
        $QueryBuilder->AppendOrderBy($Criteria);
        $QueryBuilder->AppendRange($Criteria);
    }
    
    protected function AppendGroupByClause(QueryBuilder $QueryBuilder, array $Expressions) {
        $QueryBuilder->Append(' GROUP BY ');
        foreach($QueryBuilder->Delimit($Expressions, ', ') as $Expression) {            
            $QueryBuilder->AppendExpression($Expression);
        }
    }
    
    protected function AppendHavingClause(QueryBuilder $QueryBuilder, array $Expressions) {
        $QueryBuilder->Append(' HAVING ');
        foreach($QueryBuilder->Delimit($Expressions, ' AND ') as $Expression) {            
            $QueryBuilder->AppendExpression($Expression);
        }
    }

    public function AppendUpdate(QueryBuilder $QueryBuilder, Relational\Update $Update) {
        $Criteria = $Update->GetCriteria();
        
        $QueryBuilder->Append('UPDATE ');
        $QueryBuilder->AppendTableDefinition($Criteria);
        $QueryBuilder->Append(' SET ');
        
        foreach($QueryBuilder->Delimit($Update->GetExpressions(), ',') as $Expression) {
            $QueryBuilder->AppendExpression($Expression);
        }
        
        $this->AppendCriteria($QueryBuilder, $Criteria);
    }

    public function AppendDelete(QueryBuilder $QueryBuilder, Relational\Delete $Delete) {
        $Criteria = $Delete->GetCriteria();
        
        $QueryBuilder->AppendIdentifiers('DELETE # ', array_keys($Delete->GetTables()), ',');
        $QueryBuilder->Append(' FROM ');
        
        $QueryBuilder->AppendTableDefinition($Criteria);
        
        $this->AppendCriteria($QueryBuilder, $Criteria);
    }
    
    protected function AppendCriteria(QueryBuilder $QueryBuilder, Relational\Criteria $Criteria) {
        $QueryBuilder->AppendWhere($Criteria);
        $QueryBuilder->AppendOrderBy($Criteria);
        $QueryBuilder->AppendRange($Criteria);
    }
}

?>