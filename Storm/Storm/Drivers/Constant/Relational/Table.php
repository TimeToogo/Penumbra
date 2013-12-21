<?php

namespace Storm\Drivers\Constant\Relational;

use \Storm\Core\Containers\Registrar;
use \Storm\Drivers\Base\Relational;
use \Storm\Core\Relational\Database as CoreDatabase;
use \Storm\Drivers\Base\Relational\Columns\IColumnSet;

abstract class Table extends Relational\Table {
    use \Storm\Drivers\Constant\Helpers\PropertyReflection;

    protected abstract function CreateTableStructure(IColumnSet $Column);
    protected abstract function CreateRelationalStructure(CoreDatabase $Database);
    protected abstract function CreateRelations(CoreDatabase $Database);
    
    final protected function OnInitializeStructure(CoreDatabase $Context) {
        $this->CreateTableStructure($Context->GetPlatform()->GetColumnSet());
        parent::OnInitializeStructure($Context);
    }
    
    protected function OnInitializeRelatedStructure(CoreDatabase $Context) {
        $this->CreateRelationalStructure($Context);
        parent::OnInitializeRelatedStructure($Context);
    }
    
    final protected function OnInitializeRelations(CoreDatabase $Context) {
        $this->CreateRelations($Context);
        parent::OnInitializeRelations($Context);
    }
    
    final protected function RegisterColumnStructure(Registrar $Registrar, IColumnSet $Column) {
        $this->LoadRegistrarFromProperties($Registrar, true);
    }

    final protected function RegisterStructuralTraits(Registrar $Registrar) {
        $this->LoadRegistrarFromProperties($Registrar);
    }
    
    final protected function RegisterRelationalTraits(Registrar $Registrar, CoreDatabase $Context) {
        $this->LoadRegistrarFromProperties($Registrar);
    }

    final protected function RegisterToOneRelations(Registrar $Registrar, CoreDatabase $Context) {
        $this->LoadRegistrarFromProperties($Registrar);
    }
    
    final protected function RegisterToManyRelations(Registrar $Registrar, CoreDatabase $Context) {
        $this->LoadRegistrarFromProperties($Registrar);
    }
}

?>