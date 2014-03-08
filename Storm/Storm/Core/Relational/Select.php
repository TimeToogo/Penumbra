<?php

namespace Storm\Core\Relational;

/**
 * The request represents a range of rows to load specified by the criterion.
 * This can be thought of as a SELECT 
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Select {    
    /**
     * The columns to load from.
     * 
     * @var IColumn[]
     */
    private $Columns = [];
    
    
    /**
     * @var Expression[] 
     */
    private $AggregatePredicateExpressions = [];
    
    /**
     * @var Expression[]
     */
    private $GroupByExpressions = [];
    
    /**
     * @var Criterion
     */
    private $Criterion;
    
    public function __construct(Criterion $Criterion) {
        $this->Criterion = $Criterion;
    }
    
    final public function HasColumn(IColumn $Column) {
        return isset($this->Columns[$Column->GetIdentifier()]);
    }
    
    /**
     * Add a column to the request.
     * 
     * @param IColumn $Column The column to add
     * @return void
     */
    final public function AddColumn(IColumn $Column) {
        if(!$this->Criterion->HasTable($Column->GetTable()->GetName())) {
            throw new RelationalException(
                    'Cannot add column \'%s\' to relational request the parent table table \'%s\' has not part of the request',
                    $Column->GetName(),
                    $Column->GetTable()->GetName());
        }
        $this->Columns[$Column->GetIdentifier()] = $Column;
    }
        
    /**
     * Add an array of columns to the request.
     * 
     * @param IColumn[] $Column The columns to add
     * @return void
     */
    final public function AddColumns(array $Columns) {
        array_walk($Columns, [$this, 'AddColumn']);
    }
    
    final public function RemoveColumn(IColumn $Column) {
        unset($this->Columns[$Column->GetIdentifier()]);
    }
    
    final public function RemoveColumns(array $Columns) {
        array_walk($Columns, [$this, 'RemoveColumn']);
    }
    
    /**
     * @return IColumn[]
     */
    final public function GetColumns() {
        return $this->Columns;
    }
    
    /**
     * @return ITable[]
     */
    final public function GetTables() {
        return $this->Criterion->GetAllTables();
    }
    
    /**
     * @return Criterion
     */
    final public function GetCriterion() {
        return $this->Criterion;
    }
        
    // <editor-fold defaultstate="collapsed" desc="Grouping">

    /**
     * @return boolean
     */
    final public function IsGrouped() {
        return count($this->GroupByExpressions) > 0;
    }

    /**
     * @return Expression[]
     */
    final public function GetGroupByExpressions() {
        return $this->GroupByExpressions;
    }

    final public function AddGroupByExpression(Expression $Expression) {
        $this->GroupByExpressions[] = $Expression;
    }


    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Aggregate Constraints">
    
    /**
     * @return boolean
     */
    final public function IsAggregateConstrained() {
        return count($this->AggregatePredicateExpressions) > 0;
    }

    /**
     * @return Expression[]
     */
    final public function GetAggregatePredicateExpressions() {
        return $this->AggregatePredicateExpressions;
    }

    final public function AddAggregatePredicateExpression(Expression $PredicateExpression) {
        $this->AggregatePredicateExpressions[] = $PredicateExpression;
    }

    // </editor-fold>
}

?>