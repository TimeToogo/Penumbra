<?php

namespace Storm\Core\Relational;

class Request {
    private $Tables = array();
    private $Columns = array();
    private $Predicates = array();
    private $OrderedExpressionsAscendingMap;
    private $IsSingleRow;
    private $RangeOffset;
    private $RangeAmount;
    
    public function __construct(array $Columns, $IsSingleRow) {
        foreach($Columns as $Column) {
            $this->AddColumn($Column);
        }
        $this->OrderedExpressionsAscendingMap = new \SplObjectStorage();
        $this->IsSingleRow = $IsSingleRow;
        $this->RangeOffset = 0;
        $this->RangeAmount = null;
    }
    
    final public function AddColumn(IColumn $Column) {
        $this->Columns[$Column->GetIdentifier()] = $Column;
        $this->AddTable($Column->GetTable());
    }
    
    final public function AddColumns(array $Columns) {
        array_map([$this, 'AddColumn'], $Columns);
    }
    
    /**
     * @return Table[]
     */
    final public function GetTables() {
        return $this->Tables;
    }
    
    final public function AddTable(Table $Table) {
        $this->Tables[$Table->GetName()] = $Table;
    }
    
    final public function AddTables(array $Tables) {
        array_walk($Tables, [$this, 'AddTable']);
    }
    
    /**
     * @return IColumn[]
     */
    final public function GetColumns() {
        return $this->Columns;
    }
    
    
    final public function IsConstrained() {
        foreach($this->Predicates as $Predicate) {
            if(!$Predicate->IsEmpty()) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * @return Constraints\Predicate
     */
    public function Predicate() {
        return Constraints\Predicate::On($this->Columns);
    }
    
    /**
     * @return Constraints\Predicate[]
     */
    final public function GetPredicates() {
        return array_filter($this->Predicates, function ($Predicate) { return !$Predicate->IsEmpty(); });
    }
    final public function AddPredicate(Constraints\Predicate $Predicate) {
        $this->Predicates[] = $Predicate;
    }
    
    final public function IsOrdered() {
        return $this->OrderedExpressionsAscendingMap->count() > 0;
    }
    /**
     * @return \SplObjectStorage
     */
    final public function GetOrderedExpressionsAscendingMap() {
        return $this->OrderedExpressionsAscendingMap;
    }
    final public function AddOrderByExpression(Expressions\Expression $Expression, $Ascending) {
        $this->OrderedExpressionsAscendingMap[$Expression] = $Ascending;
    }
    
    final public function IsSingleRow() {
        return $this->IsSingleRow;
    }
    final public function IsRanged() {
        if(!$this->IsSingleRow)
            return $this->RangeOffset !== 0 || $this->RangeAmount !== null;
        else 
            return true;
    }
    final public function GetRangeOffset() {
        if($this->IsSingleRow)
            return 0;
        else
            return $this->RangeOffset;
    }
   final  public function SetRangeOffset($RangeOffset) {
        $this->RangeOffset = $RangeOffset;
    }
    
    final public function GetRangeAmount() {
        if($this->IsSingleRow)
            return 1;
        else
            return $this->RangeAmount;
    }
    final public function SetRangeAmount($RangeAmount) {
        $this->RangeAmount = $RangeAmount;
    }
    
    /**
     * @return ResultRow
     */
    final public function ResultRow(array $ColumnData, $VerifiedData = false) {
        return new ResultRow($this->Columns, $ColumnData, $VerifiedData);
    }
}

?>