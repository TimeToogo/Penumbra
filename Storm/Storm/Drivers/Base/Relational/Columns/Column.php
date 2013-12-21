<?php

namespace Storm\Drivers\Base\Relational\Columns;

use \Storm\Core\Relational\IColumn;
use \Storm\Core\Relational\ColumnData;

class Column implements IColumn {
    use \Storm\Core\Helpers\Type;
    
    private $Name;
    private $DataType;
    private $Traits = array();
    
    public function __construct($Name, DataType $DataType, array $Traits = array()) {
        $this->Name = $Name;
        $this->DataType = $DataType;
        foreach($Traits as $Trait) {
            $this->AddTrait($Trait);
        }
    }

    final public function GetName() {
        return $this->Name;
    }
    
    final public function SetName($Name) {
        $this->Name = $Name;
    }

    final public function GetDataType() {
        return $this->DataType;
    }
    final public function GetDataTypeParameters() {
        return $this->DataType->GetParameters();
    }
    
    /**
     * @return ColumnTrait[]
     */
    final public function GetTraits() {
        return $this->Traits;
    }
    
    final public function AddTrait(ColumnTrait $Trait) {
        if(!$Trait->AllowMultiple() && $this->HasTrait($Trait->GetType()))
            throw new \InvalidArgumentException('Cannot contain duplicate traits of type: ' . $Trait->GetType());
        else
            $this->Traits[] = $Trait;
    }
    
    final public function HasTrait($Type) {
        foreach($this->Traits as $Trait) {
            if($Trait instanceof $Type) {
                return true;
            }
        }
        return false;
    }

    final public function Is(Column $Column) {
        if($this->Name !== $Column->Name)
            return false;
        else if(!$this->DataType->Is($Column->DataType))
            return false;
        else {
            if(count($this->Traits) !== count($Column->Traits))
                return false;
            
            $OtherTraits = $Column->Traits;
            foreach($this->Traits as $Trait) {
                foreach($OtherTraits as $Key => $OtherTrait) {
                    if($Trait->Is($OtherTrait)) {
                        unset($OtherTraits[$Key]);
                        continue 2;
                    }             
                }
                return false;
            }
            
            return true;
        }
            
    }

    public function Retrieve(ColumnData $Data) {
        return $this->DataType->ToPropertyValue($Data[$this]);
    }
    
    public function Store(ColumnData $Data, $Value) {
        $Data->SetColumn($this, $this->DataType->ToPersistedValue($Value));
    }
}
?>