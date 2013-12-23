<?php

namespace StormTests\One\Relational\Tables;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational\Database;
use \Storm\Drivers\Constant\Relational;
use \Storm\Drivers\Base\Relational\Columns\IColumnSet;
use \Storm\Drivers\Base\Relational\PrimaryKeys\IKeyGeneratorSet;
use \Storm\Drivers\Platforms\Mysql;
use \Storm\Drivers\Base\Relational\Columns\Column;
use \Storm\Drivers\Base\Relational\Columns\DataType;
use \Storm\Drivers\Base\Relational\Traits;
use \Storm\Drivers\Base\Relational\Relations;

class Tags extends Relational\Table {
    protected function Name() {
        return 'Tags';
    }

    protected function KeyGenerator(IKeyGeneratorSet $KeyGenerator) {
        return $KeyGenerator->Guid();
    }
    
    public $Id;
    public $Name;
    
    public $PrimaryKey;
    
    protected function CreateTableStructure(IColumnSet $Column) {
        $this->Id = new Column('Id', new Mysql\Columns\DataTypes\HexedBinaryDataType(16));
        $this->Name = new Column('Name', new DataType('VARCHAR', [50]));
        
        $this->PrimaryKey = new Traits\PrimaryKey([$this->Id]);
    }

    protected function CreateRelationalStructure(Database $Database) {
        
    }

    protected function CreateRelations(Database $Database) {
        
    }

}

?>
