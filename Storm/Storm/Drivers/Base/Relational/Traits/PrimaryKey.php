<?php

namespace Storm\Drivers\Base\Relational\Traits;

use \Storm\Drivers\Base\Relational\StructuralTableTrait;
use \Storm\Drivers\Base\Relational\Columns\Traits\NotNullable;

class PrimaryKey extends StructuralTableTrait {
    private $Columns;
    private $ColumnNames;
    
    public function __construct(array $Columns) {
        if(count($Columns) === 0) {
            throw new \Exception;//TODO:error message
        }
        $this->Columns = array_values($Columns);
        foreach($this->Columns as $Column) {
            if(!$Column->HasTrait(NotNullable::GetType())) {
                $Column->AddTrait(new NotNullable());
            }
        }
        $this->ColumnNames = array_map(function ($Column) { return $Column->GetName(); }, $this->Columns);
    }
    
    final public function GetColumns() {
        return $this->Columns;
    }
    
    final public function GetColumnNames() {
        return $this->ColumnNames;
    }
    
    protected function IsStructuralTrait(StructuralTableTrait $OtherTrait) {
        if(count($this->Columns) !== count($OtherTrait->Columns))
            return false;
        
        foreach($this->Columns as $Key => $Column) {
            if($Column->GetName() !== $OtherTrait->Columns[$Key]->GetName())
                return false;
        }
        
        return true;
    }
}

?>