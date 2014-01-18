<?php

namespace Storm\Core\Relational;

/**
 * Table column data represents column data from a specific table.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class TableColumnData extends ColumnData {
    /**
     * @var Table
     */
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
    
    final public function Matches(ColumnData $OtherColumnData) {
        if($OtherColumnData instanceof TableColumnData && !$this->Table->Is($OtherColumnData->Table)) {
            return false;
        }
        else {
            return parent::Matches($OtherColumnData);
        }
    }
}

?>