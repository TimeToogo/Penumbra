<?php

namespace Storm\Drivers\Platforms\Standard\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\Expressions\Expression;

class QueryCompiler implements Queries\IQueryCompiler {
    /**
     * @var ICriteriaCompiler 
     */
    private $CriteriaCompiler;
    
    /**
     * @var IResultSetSourceCompiler 
     */
    private $ResultSetSourceCompiler;
    
    public function __construct(
            ICriteriaCompiler $CriteriaCompiler = null,
            IResultSetSourceCompiler $ResultSetSourceCompiler = null) {
        $this->CriteriaCompiler = $CriteriaCompiler ?: new CriteriaCompiler();
        $this->ResultSetSourceCompiler = $ResultSetSourceCompiler ?: new ResultSetSourceCompiler();
    }
    
    private function GetColumnResolverWalker(\SplObjectStorage $SourceAliasMap) {
        $ColumnResolverWalker = new \Storm\Drivers\Platforms\Base\Queries\ColumnResolverWalker($SourceAliasMap);
        
        return $ColumnResolverWalker;
    }
    
    protected function GetSourceAliasMap(Relational\ResultSetSources $Sources) {
        $Map = new \SplObjectStorage();
        
        $Prefix = 't';
        $Map[$Sources->GetSource()] = $Prefix;
        
        $Index = 1;
        foreach ($Sources->GetJoins() as $Join) {
            $Map[$Join->GetSource()] = $Prefix . $Index;
            $Index++;
        }
        
        return $Map;
    }
    
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
        $Sources = $ResultSetSelect->GetSources();
        $SourceAliasMap = $this->GetSourceAliasMap($Sources);
        $ColumnResolverWalker = $this->GetColumnResolverWalker($SourceAliasMap);
        $QueryBuilder->AddExpressionWalker($ColumnResolverWalker);
        
        $QueryBuilder->Append('SELECT ');
        
        foreach($QueryBuilder->Delimit($ResultSetSelect->GetColumns(), ',') as $Column) {
            $ColumnExpression = Expression::Identifier([$SourceAliasMap[$Sources->GetColumnSource($Column)], $Column->GetName()]);
            
            $QueryBuilder->AppendExpression($Column->GetReviveExpression($ColumnExpression));
            $QueryBuilder->AppendIdentifier(' AS #', [$Column->GetIdentifier()]);
        }
        
        $this->AppendSelectClauses($QueryBuilder, $ResultSetSelect, $SourceAliasMap);
        
        $QueryBuilder->RemoveExpressionWalker($ColumnResolverWalker);
    }
    
    protected function AppendDataSelect(QueryBuilder $QueryBuilder, Relational\DataSelect $DataSelect) {
        $SourceAliasMap = $this->GetSourceAliasMap($DataSelect->GetSources());
        $ColumnResolverWalker = $this->GetColumnResolverWalker($SourceAliasMap);
        $QueryBuilder->AddExpressionWalker($ColumnResolverWalker);
        
        $QueryBuilder->Append('SELECT ');
        
        foreach($QueryBuilder->Delimit($DataSelect->GetAliasExpressionMap(), ',') as $Alias => $Expression) {
            $QueryBuilder->AppendExpression($Expression);
            $QueryBuilder->AppendIdentifier(' AS #', $Alias);
        }
        
        $this->AppendSelectClauses($QueryBuilder, $DataSelect, $ColumnResolverWalker);
        
        $QueryBuilder->RemoveExpressionWalker($ColumnResolverWalker);
    }
    
    protected function AppendExistsSelect(QueryBuilder $QueryBuilder, Relational\ExistsSelect $ExistsSelect) {
        $SourceAliasMap = $this->GetSourceAliasMap($ExistsSelect->GetSources());
        $ColumnResolverWalker = $this->GetColumnResolverWalker($SourceAliasMap);
        $QueryBuilder->AddExpressionWalker($ColumnResolverWalker);
        
        $QueryBuilder->Append('SELECT EXISTS (SELECT *');
        $this->AppendSelectClauses($QueryBuilder, $ExistsSelect, $SourceAliasMap);
        $QueryBuilder->Append(')');
        
        $QueryBuilder->RemoveExpressionWalker($ColumnResolverWalker);
    }
    
    protected function AppendSelectClauses(QueryBuilder $QueryBuilder, Relational\Select $Select, \SplObjectStorage $SourceAliasMap) {
        $QueryBuilder->Append(' FROM ');
        $this->ResultSetSourceCompiler->AppendResultSetSources($QueryBuilder, $Select->GetSources(), $SourceAliasMap);
        $this->AppendSelectCriteria($QueryBuilder, $Select);
    }
    
    protected function AppendSelectCriteria(QueryBuilder $QueryBuilder, Relational\Select $Select) {
        $Criteria = $Select->GetCriteria();
        
        $this->CriteriaCompiler->AppendWhere($QueryBuilder, $Criteria->GetPredicateExpressions());
        
        if($Select->IsGrouped()) {
            $this->AppendGroupByClause($QueryBuilder, $Select->GetGroupByExpressions());
        }
        if($Select->IsAggregateConstrained()) {
            $this->AppendHavingClause($QueryBuilder, $Select->GetAggregatePredicateExpressions());
        }
        
        $this->CriteriaCompiler->AppendOrderBy($QueryBuilder, $Criteria->GetOrderedExpressionsAscendingMap());
        $this->CriteriaCompiler->AppendRange($QueryBuilder, $Criteria->GetRangeOffset(), $Criteria->GetRangeAmount());
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
        $Sources = $Update->GetSources();
        $SourceAliasMap = $this->GetSourceAliasMap($Sources);
        $ColumnResolverWalker = $this->GetColumnResolverWalker($SourceAliasMap);
        $QueryBuilder->AddExpressionWalker($ColumnResolverWalker);
        
        $QueryBuilder->Append('UPDATE ');
        $this->ResultSetSourceCompiler->AppendResultSetSources($Update->GetSources());
        $QueryBuilder->Append(' SET ');
        
        $ColumnExpressionMap = $Update->GetColumnExpressionMap();
        foreach($QueryBuilder->Delimit($ColumnExpressionMap, ',') as $Column) {
            $QueryBuilder->AppendExpression(Expression::Identifier([$SourceAliasMap[$Sources->GetColumnSource($Column)], $Column->GetName()]));
            $QueryBuilder->Append($this->SetOperator());
            $QueryBuilder->AppendExpression($Column->GetPersistExpression($ColumnExpressionMap[$Column]));
        }
        
        $this->AppendCriteria($QueryBuilder, $Update->GetCriteria());
        
        $QueryBuilder->RemoveExpressionWalker($ColumnResolverWalker);
    }
    
    protected function SetOperator() {
        return '=';
    }

    public function AppendDelete(QueryBuilder $QueryBuilder, Relational\Delete $Delete) {
        $SourceAliasMap = $this->GetSourceAliasMap($Delete->GetSources());
        $ColumnResolverWalker = $this->GetColumnResolverWalker($SourceAliasMap);
        $QueryBuilder->AddExpressionWalker($ColumnResolverWalker);
        
        $QueryBuilder->AppendIdentifiers('DELETE # ', array_keys($Delete->GetTables()), ',');
        $QueryBuilder->Append(' FROM ');
        
        $this->ResultSetSourceCompiler->AppendResultSetSources($Delete->GetSources());
        
        $this->AppendCriteria($QueryBuilder, $Delete->GetCriteria());
        
        $QueryBuilder->RemoveExpressionWalker($ColumnResolverWalker);
    }
    
    protected function AppendCriteria(QueryBuilder $QueryBuilder, Relational\Criteria $Criteria) {
        $this->CriteriaCompiler->AppendWhere($QueryBuilder, $Criteria->GetPredicateExpressions());        
        $this->CriteriaCompiler->AppendOrderBy($QueryBuilder, $Criteria->GetOrderedExpressionsAscendingMap());
        $this->CriteriaCompiler->AppendRange($QueryBuilder, $Criteria->GetRangeOffset(), $Criteria->GetRangeAmount());
    }
}

?>