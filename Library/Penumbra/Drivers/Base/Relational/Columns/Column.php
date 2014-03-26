<?php

namespace Penumbra\Drivers\Base\Relational\Columns;

use \Penumbra\Core\Relational;

class Column implements Relational\IColumn {
    use \Penumbra\Core\Helpers\Type;
    
    private $Name;
    private $Identifier;
    private $IsPrimaryKey;
    private $Table;
    private $DataType;
    private $Traits = [];
    
    public function __construct($Name, DataType $DataType, $IsPrimaryKey = false, array $Traits = []) {
        $this->Name = $Name;
        $this->Identifier = $Name;
        $this->DataType = $DataType;
        $this->IsPrimaryKey = $IsPrimaryKey;
        foreach($Traits as $Trait) {
            $this->AddTrait($Trait);
        }
    }
    
    final public function GetName() {
        return $this->Name;
    }
    
    final public function SetName($Name) {
        $this->Name = $Name;
        $this->UpdateIdentifier();
    }
    
    final public function IsPrimaryKey() {
        return $this->IsPrimaryKey;
    }
    
    final public function GetIdentifier() {
        return $this->Identifier;
    }
    
    final public function GetTable() {
        return $this->Table;
    }
    
    final public function HasTable() {
        return $this->Table !== null;
    }
    
    final public function SetTable(Relational\ITable $Table = null) {
        $this->Table = $Table;
        $this->UpdateIdentifier();
    }
    
    private function UpdateIdentifier() {
        $this->Identifier = ($this->Table ? $this->Table->GetName() : '') . '.' . $this->Name;
    }
    
    /**
     * @return DataType
     */
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
            throw new Relational\RelationalException(
                    'Column cannot contain multiple traits of type %s',
                    $Trait->GetType());
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
    
    public function ToPropertyValue($Value) {
        return $this->DataType->ToPropertyValue($Value);
    }
    
    public function ToPersistenceValue($Value) {
        return $this->DataType->ToPersistedValue($Value);
    }
    
    public function GetReviveExpression(Relational\Expression $ValueExpression) {
        return $this->DataType->GetReviveExpression($ValueExpression);
    }
    
    public function GetPersistExpression(Relational\Expression $ValueExpression) {
        return $this->DataType->GetPersistExpression($ValueExpression);
    }
}
?>