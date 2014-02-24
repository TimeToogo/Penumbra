<?php

namespace Storm\Tests\Integration\ObjectRelationalModels\Blog\Relational\Tables;

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

class Authors extends Relational\Table {
    protected function Name() {
        return 'Authors';
    }
    
    protected function KeyGenerator(IKeyGeneratorSet $KeyGenerator) {
        return $KeyGenerator->Increment();
    }
    
    public $Id;
    public $FirstName;
    public $LastName;
    public $CreatedDate;
    
    protected function CreateTableStructure(IColumnSet $Column) {
        $this->Id = $Column->IncrementInt32('Id');
        $this->FirstName = $Column->String('FirstName', 50);
        $this->LastName = $Column->String('LastName', 50);
    }

    protected function CreateRelationalStructure(Database $Database) {
        
    }

    protected function CreateRelations(Database $Database) {
        
    }
}

?>
