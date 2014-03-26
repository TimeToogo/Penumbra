<?php

namespace Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Relational\Tables;

use \Penumbra\Core\Containers\Map;
use \Penumbra\Core\Relational\Database;
use \Penumbra\Drivers\Constant\Relational;
use \Penumbra\Drivers\Base\Relational\Columns\IColumnSet;
use \Penumbra\Drivers\Base\Relational\PrimaryKeys\IKeyGeneratorSet;
use \Penumbra\Drivers\Platforms\Mysql;
use \Penumbra\Drivers\Base\Relational\Columns\Column;
use \Penumbra\Drivers\Base\Relational\Columns\DataType;
use \Penumbra\Drivers\Base\Relational\Traits;
use \Penumbra\Drivers\Base\Relational\Relations;

class Tags extends Relational\Table {
    protected function Name() {
        return 'Tags';
    }

    protected function KeyGenerator(IKeyGeneratorSet $KeyGenerator) {
        return $KeyGenerator->Increment();
    }
    
    public $Id;
    public $Name;
    public $Description;
    public $Number;
    
    protected function CreateTableStructure(IColumnSet $Column) {
        $this->Id = $Column->IncrementInt32('Id');
        $this->Name = $Column->String('Name', 50);
        $this->Description = $Column->String('Description', 255);
        $this->Number = $Column->Int32('Number');
    }

    protected function CreateRelationalStructure(Database $Database) {
        
    }

    protected function CreateRelations(Database $Database) {
        
    }

}

?>
