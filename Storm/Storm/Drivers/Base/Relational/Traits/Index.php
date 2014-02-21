<?php

namespace Storm\Drivers\Base\Relational\Traits;

use \Storm\Drivers\Base\Relational\StructuralTableTrait;
use \Storm\Drivers\Base\Relational\Columns\Column;

final class IndexType {
    const Plain = 0;
    const Unique = 1;
    const FullText = 2;
}

final class IndexDirection {
    const Ascending = 0;
    const Descending = 1;
}

class Index extends StructuralTableTrait {
    private $Name;
    private $Type;
    private $Columns;
    private $ColumnNames;
    private $Directions = [];
    
    public function __construct($Name, array $Columns, $Type = IndexType::Plain) {
        $this->Name = $Name;
        $this->Columns = array_values($Columns);
        $this->ColumnNames = array_map(function ($Column) { return $Column->GetName(); }, $this->Columns);
        $this->Type = $Type;
    }
    
    final public function GetName() {
        return $this->Name;
    }
    
    final public function GetColumns() {
        return $this->Columns;
    }
    
    final public function GetColumnNames() {
        return $this->ColumnNames;
    }
    
    final protected function SetColumnDirection(Column $Column, $Direction) {
        $this->Directions[$Column->GetName()] = $Direction;
    }
    
    public function GetColumnDirection(Column $Column) {
        if(isset($this->Directions[$Column->GetName()]))
            return $this->Directions[$Column->GetName()];
        else
            return IndexDirection::Ascending;
    }

    protected function IsStructuralTrait(StructuralTableTrait $OtherTrait) {        
        if(count($this->Columns) !== count($OtherTrait->Columns))
            return false;
        
        foreach($this->Columns as $Key => $Column) {
            if($Column->GetName() !== $OtherTrait->Columns[$Key]->GetName())
                return false;
            if($this->GetColumnDirection($Column) !== $OtherTrait->GetColumnDirection($OtherTrait->Columns[$Key]))
                return false;
        }
        
        return true;
    }
}

?>