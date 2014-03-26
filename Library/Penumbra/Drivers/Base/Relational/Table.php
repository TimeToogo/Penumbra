<?php

namespace Penumbra\Drivers\Base\Relational;

use \Penumbra\Core\Containers;
use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Relational\Queries\IConnection;
use \Penumbra\Drivers\Base\Relational\Columns\IColumnSet;
use \Penumbra\Drivers\Base\Relational\PrimaryKeys\IKeyGenerator;
use \Penumbra\Drivers\Base\Relational\PrimaryKeys\IKeyGeneratorSet;

abstract class Table extends Relational\Table {
    private $KeyGenerator;
    private $Traits = [];
    private $StructuralTraits = [];
    private $RelationalTraits = [];
    
    final protected function RegisterColumns(Containers\Registrar $Registrar, Relational\Database $Database) {
        $this->RegisterColumnStructure($Registrar, $Database->GetPlatform()->GetColumnSet());
    }
    protected abstract function RegisterColumnStructure(Containers\Registrar $Registrar, IColumnSet $Column);
    
    protected function OnStructureInitialized(Relational\Database $Database) {
        $Registrar = new Containers\Registrar(StructuralTableTrait::GetType());
        $PrimaryKeyColumns = $this->GetPrimaryKeyColumns();
        if(count($PrimaryKeyColumns) > 0) {
            $Registrar->Register(new Traits\PrimaryKey($PrimaryKeyColumns));
        }
        $this->RegisterStructuralTraits($Registrar);
        foreach($Registrar->GetRegistered() as $Trait) {
            $this->AddTrait($Trait);
        }
        
        $this->KeyGenerator = $this->KeyGenerator($Database->GetPlatform()->GetKeyGeneratorSet());
        if($this->KeyGenerator !== null) {
            $this->KeyGenerator->SetTable($this);
        }
    }
    protected abstract function RegisterStructuralTraits(Containers\Registrar $Registrar);
    
    final public function InitializeRelatedStructure(Relational\Database $Database) {
        $this->OnInitializeRelatedStructure($Database);
        
        $Registrar = new Containers\Registrar(RelationalTableTrait::GetType());
        $this->RegisterRelationalTraits($Registrar, $Database);
        foreach($Registrar->GetRegistered() as $Trait) {
            $this->AddTrait($Trait);
        }
        
        $this->OnRelatedStructureInitialized($Database);
    }
    protected function OnInitializeRelatedStructure(Relational\Database $Database) { }
    protected function OnRelatedStructureInitialized(Relational\Database $Database) { }
    
    protected abstract function RegisterRelationalTraits(Containers\Registrar $Registrar, Relational\Database $Context);
    
    /**
     * @return IKeyGenerator|null
     */
    protected function KeyGenerator(IKeyGeneratorSet $KeyGenerator) {
        return null;
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
            throw new \Penumbra\Core\UnexpectedValueException(
                    'The supplied trait must derive from either %s or %s: %s given',
                    StructuralTableTrait::GetType(),
                    RelationalTableTrait::GetType(),
                    $Trait->GetType());
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