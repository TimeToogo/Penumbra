<?php

namespace StormExamples\One\Relational\Tables;

use \Storm\Core\Relational\Database;
use \Storm\Drivers\Constant\Relational;
use \Storm\Drivers\Base\Relational\Columns\IColumnSet;
use \Storm\Drivers\Base\Relational\PrimaryKeys\IKeyGeneratorSet;

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
