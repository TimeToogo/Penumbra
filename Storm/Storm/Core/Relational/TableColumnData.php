<?php

namespace Storm\Core\Relational;

abstract class TableColumnData extends ColumnData {
    private $Table;
    
    protected function __construct(Table $Table, array $ColumnData = array(), $DataVerified = false) {
        $this->Table = $Table;
        parent::__construct($ColumnData, $DataVerified);
    }
    
    /**
     * @return Table
     */
    final public function GetTable() {
        return $this->Table;
    }
    
    protected function AddColumn($ColumnName, $Data) {
        if(!$this->Table->HasColumn($ColumnName))
            throw new \InvalidArgumentException('$ColumnOrColumnName must be a column of table ' . $this->Table->GetName());
        
        parent::AddColumn($ColumnName, $Data);
    }
    
    final public function Matches(ColumnData $Data) {
        if(!$this->Table->Is($Data->Table))
            return false;
        else
            return parent::Matches($Data);
    }
}

?>