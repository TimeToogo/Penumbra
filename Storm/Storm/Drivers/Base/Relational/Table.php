<?php

namespace Storm\Drivers\Base\Relational;

use \Storm\Core\Containers;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\Columns\IColumnSet;
use \Storm\Drivers\Base\Relational\PrimaryKeys\IKeyGeneratorSet;

abstract class Table extends Relational\Table {
    private $KeyGenerator;
    private $Traits = array();
    private $StructuralTraits = array();
    private $RelationalTraits = array();
    
    final protected function RegisterColumns(Containers\Registrar $Registrar, Relational\Database $Context) {
        $this->RegisterColumnStructure($Registrar, $Context->GetPlatform()->GetColumnSet());
    }
    protected abstract function RegisterColumnStructure(Containers\Registrar $Registrar, IColumnSet $Column);
    
    protected function OnStructureInitialized(Relational\Database $Context) {
        
        $this->KeyGenerator = $this->KeyGenerator($Context->GetPlatform()->GetKeyGeneratorSet());
        
        $Registrar = new Containers\Registrar(StructuralTableTrait::GetType());
        $this->RegisterStructuralTraits($Registrar);
        foreach($Registrar->GetRegistered() as $Trait) {
            $this->AddTrait($Trait);
        }
    }
    protected abstract function RegisterStructuralTraits(Containers\Registrar $Registrar);
    
    final public function InitializeRelatedStructure(Relational\Database $Context) {
        $this->OnInitializeRelatedStructure($Context);
        
        $Registrar = new Containers\Registrar(RelationalTableTrait::GetType());
        $this->RegisterRelationalTraits($Registrar, $Context);
        foreach($Registrar->GetRegistered() as $Trait) {
            $this->AddTrait($Trait);
        }
        
        $this->OnRelatedStructureInitialized($Context);
    }
    protected function OnInitializeRelatedStructure(Relational\Database $Context) { }
    protected function OnRelatedStructureInitialized(Relational\Database $Context) { }
    
    protected abstract function RegisterRelationalTraits(Containers\Registrar $Registrar, Relational\Database $Context);
        
    protected abstract function KeyGenerator(IKeyGeneratorSet $KeyGenerator);
    final public function GeneratePrimaryKeys(IConnection $Connection, $Amount = 1) {
        $PrimaryKeys = array();
        for($Count = 0; $Count < $Amount; $Count++) {
            $PrimaryKeys[] = $this->PrimaryKey();
        }
        
        $this->KeyGenerator->FillPrimaryKeys($Connection, $this, $PrimaryKeys, $this->GetPrimaryKeyColumns());
        
        return $PrimaryKeys;
    }
    
    final protected function ColumnIsPrimaryKey(Relational\IColumn $Column) {
        foreach ($this->Traits as $Trait) {
            if($Trait instanceof Traits\PrimaryKey)
                if(array_search($Column, $Trait->GetColumns()) !== false)
                        return true;
        }
        return false;
    }
    
    /**
     * @return TableTrait[]
     */
    final public function GetTraits() {
        return $this->Traits;
    }
    
    final public function AddTrait(TableTrait $Trait) {
        if($Trait instanceof StructuralTableTrait) {
            $this->StructuralTraits[] = $Trait;
            $this->Traits[] = $Trait;
        }
        else if ($Trait instanceof RelationalTableTrait) {
            $this->RelationalTraits[] = $Trait;
            $this->Traits[] = $Trait;
        }
        else {
            throw new \InvalidArgumentException('Invalid TableTrait');
        }
    }
    
    /**
     * @return StructuralTableTrait[]
     */
    final public function GetStructuralTraits() {
        return $this->StructuralTraits;
    }
    
    /**
     * @return StructuralTableTrait[]
     */
    final public function GetRelationalTraits() {
        return $this->RelationalTraits;
    }
}

?>