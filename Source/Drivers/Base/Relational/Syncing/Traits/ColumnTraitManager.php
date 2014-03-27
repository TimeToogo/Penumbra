<?php

namespace Penumbra\Drivers\Base\Relational\Syncing\Traits;

use \Penumbra\Drivers\Base\Relational\Columns\ColumnTrait;
use \Penumbra\Drivers\Base\Relational\Queries\QueryBuilder;

abstract class ColumnTraitManager implements IColumnTraitManager {
    private $AppenderFunctionManager;
    public function __construct() {
        $this->AppenderFunctionManager = new TraitMethodManager();
        $this->Initialize();
    }
    protected abstract function Initialize();
    
    final protected function Register($TraitType, callable $AppenderFunction) {
        $this->AppenderFunctionManager->Register($TraitType, $AppenderFunction);
    }

    final public function AppendDefinition(QueryBuilder $QueryBuilder, ColumnTrait $Trait) {
        $AppenderFunction = $this->AppenderFunctionManager->GetRegisteredFunction($Trait);
        $AppenderFunction($QueryBuilder, $Trait);
    }
}

?>