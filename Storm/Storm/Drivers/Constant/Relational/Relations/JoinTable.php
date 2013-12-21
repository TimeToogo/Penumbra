<?php

namespace Storm\Drivers\Constant\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Containers\Registrar;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Columns\IColumnSet;
use \Storm\Drivers\Base;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

abstract class JoinTable extends Base\Relational\Relations\JoinTable {
    use \Storm\Drivers\Constant\Helpers\PropertyReflection;

    protected abstract function CreateTableStructure(IColumnSet $Column);
    
    protected function OnInitializeStructure(Relational\Database $Context) {
        $this->CreateTableStructure($Context->GetPlatform()->GetColumnSet());
        parent::OnInitializeStructure($Context);
    }
    
    final protected function RegisterColumnStructure(Registrar $Registrar, IColumnSet $Column) {
        $this->LoadRegistrarFromProperties($Registrar, true);
    }
}

?>