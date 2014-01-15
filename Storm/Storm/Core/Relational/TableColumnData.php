<?php

namespace Storm\Core\Relational;

abstract class TableColumnData extends ColumnData {
    private $Table;
    
    protected function __construct(Table $Table, array $ColumnData = array()) {
        $this->Table = $Table;
        parent::__construct($Table->GetColumns(), $ColumnData);
    }
    
    /**
     * @return Table
     */
    final public function GetTable() {
        return $this->Table;
    }
    
    final public function Matches(TableColumnData $OtherTableColumnData) {
        if(!$this->Table->Is($OtherTableColumnData->Table))
            return false;
        else
            return parent::Matches($OtherTableColumnData);
    }
}

?>