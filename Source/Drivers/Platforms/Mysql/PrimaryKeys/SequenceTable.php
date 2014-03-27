<?php

namespace Penumbra\Drivers\Platforms\Mysql\PrimaryKeys;

use \Penumbra\Core\Containers\Registrar;
use \Penumbra\Core\Relational\Database;
use \Penumbra\Drivers\Base\Relational;
use \Penumbra\Drivers\Base\Relational\Columns;
use \Penumbra\Drivers\Base\Relational\Columns\Column;
use \Penumbra\Drivers\Base\Relational\Columns\DataType;
use \Penumbra\Drivers\Platforms\Mysql;
use \Penumbra\Drivers\Base\Relational\Queries\IConnection;

class SequenceTable extends Relational\Table {
    private $Name;
    
    public $SequenceNameColumn;
    public $IncrementColumn;
    
    public function __construct($Name) {
        $this->Name = $Name;
        
        parent::__construct();
    }
        
    protected function Name() {
        return $this->Name;
    }
    protected function RegisterColumnStructure(Registrar $Registrar, Columns\IColumnSet $Column) {
        $this->SequenceNameColumn = $Column->String('Sequence', 64, true);
        $this->IncrementColumn = $Column->IncrementInt64('Increment', true);
        
        $Registrar->Register($this->SequenceNameColumn);
        $Registrar->Register($this->IncrementColumn);
    }
    
    protected function RegisterStructuralTraits(Registrar $Registrar) {
        $Registrar->Register(new Mysql\Tables\Engine('MYISAM'));
    }
    
    protected function RegisterRelationalTraits(Registrar $Registrar, Database $Context) { }

    protected function RegisterToManyRelations(Database $Context, Registrar $Registrar) { }
    protected function RegisterToOneRelations(Database $Context, Registrar $Registrar) { }
}

?>
