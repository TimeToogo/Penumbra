<?php

namespace Storm\Drivers\Dynamic\Relational;

use \Storm\Core\Containers\Map;
use \Storm\Core\Containers\Registrar;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Columns\IColumnSet;
use \Storm\Drivers\Base\Relational\PrimaryKeys\IKeyGeneratorSet;

class JoinTable extends \Storm\Drivers\Base\Relational\Relations\JoinTable {
    private $Name;
    private $_Columns = [];
    private $Table1;
    private $ForeignKey1Map = [];
    private $Table2;
    private $ForeignKey2Map = [];
    
    public function __construct($Name, Relational\ITable $Table1, Relational\ITable $Table2) {
        $this->Name = $Name;
        $this->Table1 = $Table1;
        $this->Table2 = $Table2;
        $this->AddColumns($Table1, $this->ForeignKey1Map);
        $this->AddColumns($Table2, $this->ForeignKey2Map);
        parent::__construct();
    }
    
    private function AddColumns(Relational\Table $Table, array &$ForeignKeyMap) {
        foreach($Table->GetPrimaryKeyColumns() as $Column) {
            $ClonedColumn = clone $Column;
            $ColumnName = $this->MakeColumnName($Table, $Column);
            $ClonedColumn->SetName($ColumnName);
            $this->_Columns[$ColumnName] = $ClonedColumn;
            $ForeignKeyMap[$ColumnName] = $Column->GetName();
        }
    }
    
    private function MakeColumnName(Relational\Table $Table, Relational\IColumn $Column) {
        return $Table->GetName() . '_' . $Column->GetName();
    }
    
    protected function Name() {
        return $this->Name;
    }
    
    protected function Table1(Relational\Database $Database) {
        return $this->Table1;
    }

    protected function Table2(Relational\Database $Database) {
        return $this->Table2;
    }
    
    protected function RegisterColumnStructure(Registrar $Registrar, IColumnSet $Column) {
        $Registrar->RegisterAll($this->_Columns);
    }
    
    protected function MapForeignKey1(Map $Map, Relational\Table $Table1) {
        foreach($this->ForeignKey1Map as $ColumnName => $ReferencedColumnName) {
            $Map->Map($this->_Columns[$ColumnName], $Table1->GetColumn($ReferencedColumnName));
        }
    }

    protected function MapForeignKey2(Map $Map, Relational\Table $Table2) {
        foreach($this->ForeignKey1Map as $ColumnName => $ReferencedColumnName) {
            $Map->Map($this->_Columns[$ColumnName], $Table2->GetColumn($ReferencedColumnName));
        }
    }

}

?>