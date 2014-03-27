<?php

namespace Penumbra\Drivers\Dynamic\Relational;

use \Penumbra\Core\Containers\Registrar;
use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Relational\Columns\IColumnSet;
use \Penumbra\Drivers\Base\Relational\PrimaryKeys\IKeyGeneratorSet;
use \Penumbra\Drivers\Base\Relational\PrimaryKeys\IKeyGenerator;

class Table extends \Penumbra\Drivers\Base\Relational\Table {
    private $Name;
    private $_Columns;
    private $KeyGenerator;
    private $StructuralTraits;
    private $RelationalTraits;
    private $ToOneRelations;
    private $ToManyRelations;
    
    public function __construct(
            $Name,
            IKeyGenerator $KeyGenerator = null,
            array $Columns, 
            array $StructuralTraits = [], 
            array $RelationalTraits = [], 
            array $ToOneRelations = [], 
            array $ToManyRelations = []) {
        $this->Name = $Name;
        $this->_Columns = $Columns;
        $this->KeyGenerator = $KeyGenerator;
        $this->StructuralTraits = $StructuralTraits;
        $this->RelationalTraits = $RelationalTraits;
        $this->ToOneRelations = $ToOneRelations;
        $this->ToManyRelations = $ToManyRelations;
        $this->InitializeStructure(new Database([]));
        
        parent::__construct();
    }
    
    final protected function Name() {
        return $this->Name;
    }
    
    final protected function KeyGenerator(IKeyGeneratorSet $KeyGenerator) {
        return $this->KeyGenerator;
    }

    final protected function RegisterColumnStructure(Registrar $Registrar, IColumnSet $Column) {
        $Registrar->RegisterAll($this->_Columns);
    }

    final protected function RegisterStructuralTraits(Registrar $Registrar) {
        $Registrar->RegisterAll($this->StructuralTraits);
    }

    final protected function RegisterRelationalTraits(Registrar $Registrar, Relational\Database $Context) {
        $Registrar->RegisterAll($this->RelationalTraits);
    }

    final protected function RegisterToOneRelations(Registrar $Registrar, Relational\Database $Context) {
        $Registrar->RegisterAll($this->ToOneRelations);
    }

    final protected function RegisterToManyRelations(Registrar $Registrar, Relational\Database $Context) {
        $Registrar->RegisterAll($this->ToManyRelations);
    }
}

?>