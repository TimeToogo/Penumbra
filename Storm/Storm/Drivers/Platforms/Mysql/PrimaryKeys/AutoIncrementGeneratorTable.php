<?php

namespace Storm\Drivers\Platforms\Mysql\PrimaryKeys;

use \Storm\Core\Containers\Registrar;
use \Storm\Core\Relational\Database;
use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Columns\Column;
use \Storm\Drivers\Base\Relational\Columns\DataType;
use \Storm\Drivers\Platforms\Mysql;
use \Storm\Drivers\Base\Relational\Queries\IConnection;

class AutoIncrementGeneratorTable extends Relational\Table {
    private $Name;
    
    private $TableNameColumn;
    private $IncrementColumn;
    
    public function __construct($Name) {
        $this->Name = $Name;
        
        $this->TableNameColumn = new Column('Table', new DataType('VARCHAR', [64]));
        $this->IncrementColumn = new Column('Increment', new DataType('INT', [11]), [new Columns\Traits\Increment()]);
        
        parent::__construct();
    }
    
    protected function KeyGenerator() {
        return new \Storm\Drivers\Platforms\Null\NullKeyGenerator();
    }
    
    protected function Name() {
        return $this->Name;
    }
    protected function RegisterColumnStructure(Registrar $Registrar, Columns\IColumnSet $Column) {
        $Registrar->Register($this->TableNameColumn);
        $Registrar->Register($this->IncrementColumn);
    }
    
    protected function RegisterStructuralTraits(Registrar $Registrar) {
        $Registrar->Register(new Mysql\Tables\Engine('MYISAM'));
        
        $Registrar->Register(new Relational\Traits\PrimaryKey
                ([$this->TableNameColumn, $this->IncrementColumn]));
    }
    
    protected function RegisterRelationalTraits(Registrar $Registrar, Database $Context) { }

    protected function RegisterToManyRelations(Database $Context, Registrar $Registrar) { }
    protected function RegisterToOneRelations(Database $Context, Registrar $Registrar) { }
}

?>
