<?php

namespace Storm\Drivers\Base\Relational;

use \Storm\Core\Containers;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\Columns\IColumnSet;
use \Storm\Drivers\Base\Relational\PrimaryKeys\IKeyGenerator;
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
        $Registrar = new Containers\Registrar(StructuralTableTrait::GetType());
        $Registrar->Register(new Traits\PrimaryKey($this->GetPrimaryKeyColumns()));
        $this->RegisterStructuralTraits($Registrar);
        foreach($Registrar->GetRegistered() as $Trait) {
            $this->AddTrait($Trait);
        }
        
        $this->KeyGenerator = $this->KeyGenerator($Context->GetPlatform()->GetKeyGeneratorSet());
        if($this->KeyGenerator !== null) {
            $this->KeyGenerator->SetTable($this);
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
    
    /**
     * @return IKeyGenerator
     */
    protected abstract function KeyGenerator(IKeyGeneratorSet $KeyGenerator);
    
    private function AddTrait(TableTrait $Trait) {
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
     * @return TableTrait[]
     */
    final public function GetTraits() {
        return $this->Traits;
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
    
    public function HasKeyGenerator() {
        return $this->KeyGenerator !== null;
    }
    
    /**
     * @return IKeyGenerator
     */
    public function GetKeyGenerator() {
        return $this->KeyGenerator;
    }
}

?>