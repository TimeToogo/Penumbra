<?php

namespace Storm\Drivers\Base\Relational\Syncing\Traits;

use Storm\Drivers\Base\Relational;
use Storm\Drivers\Base\Relational\TableTrait;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;

abstract class TableTraitManager implements ITableTraitManager {
    private $AddFunctionManager;
    private $DropFunctionManager;
    private $DefinitionFunctionManager;
    public function __construct() {
        $this->AddFunctionManager = new TraitMethodManager();
        $this->DropFunctionManager = new TraitMethodManager();
        $this->DefinitionFunctionManager = new TraitMethodManager();
        $this->Initialize();
    }
    protected abstract function Initialize();
    
    final protected function Register($TraitType, callable $DefinitionFunction, callable $AddFunction, callable $DropFunction) {
        $this->RegisterDefinition($TraitType, $DefinitionFunction);
        $this->RegisterAdd($TraitType, $AddFunction);
        $this->RegisterDrop($TraitType, $DropFunction);
    }
    
    final protected function RegisterDefinition($TraitType, callable $AppenderFunction) {
        $this->DefinitionFunctionManager->Register($TraitType, $AppenderFunction);
    }
    
    final protected function RegisterAdd($TraitType, callable $AppenderFunction) {
        $this->AddFunctionManager->Register($TraitType, $AppenderFunction);
    }
    
    final protected function RegisterDrop($TraitType, callable $AppenderFunction) {
        $this->DropFunctionManager->Register($TraitType, $AppenderFunction);
    }
    
    public function AppendAdd(IConnection $Connection, 
            QueryBuilder $QueryBuilder, Relational\Table $Table, TableTrait $Trait) {
        $AppenderFunction = $this->AddFunctionManager->GetRegisteredFunction($Trait);
        $AppenderFunction($Connection, $QueryBuilder, $Table, $Trait);
    }

    public function AppendDrop(IConnection $Connection, 
            QueryBuilder $QueryBuilder, Relational\Table $Table, TableTrait $Trait) {
        $AppenderFunction = $this->DropFunctionManager->GetRegisteredFunction($Trait);
        $AppenderFunction($Connection, $QueryBuilder, $Table, $Trait);
    }
    
    public function AppendDefinition(QueryBuilder $QueryBuilder, Relational\Table $Table, TableTrait $Trait) {
        $AppenderFunction = $this->DefinitionFunctionManager->GetRegisteredFunction($Trait);
        $AppenderFunction($QueryBuilder, $Table, $Trait);
    }
}

?>