<?php

namespace Penumbra\Drivers\Constant\Relational\Relations;

use \Penumbra\Core\Containers\Map;
use \Penumbra\Core\Containers\Registrar;
use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Relational\Columns\IColumnSet;
use \Penumbra\Drivers\Base;
use \Penumbra\Drivers\Base\Relational\Traits\ForeignKey;

abstract class JoinTable extends Base\Relational\Relations\JoinTable {
    use \Penumbra\Drivers\Constant\Helpers\PropertyReflection;

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