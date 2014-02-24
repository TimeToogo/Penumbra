<?php

namespace Storm\Tests\Integration\ObjectRelationalModels\Blog\Relational\Tables;

use \Storm\Core\Relational\Database;
use \Storm\Drivers\Constant\Relational;
use \Storm\Drivers\Base\Relational\Columns\IColumnSet;
use \Storm\Drivers\Base\Relational\PrimaryKeys\IKeyGeneratorSet;
use \Storm\Drivers\Platforms\Mysql;
use \Storm\Drivers\Base\Relational\Traits;
use \Storm\Drivers\Base\Relational\Columns\Column;
use \Storm\Drivers\Base\Relational\Columns\DataType;
use \Storm\Drivers\Base\Relational\Relations;

class Blogs extends Relational\Table {
    protected function Name() {
        return 'Blogs';
    }
    
    protected function KeyGenerator(IKeyGeneratorSet $KeyGenerator) {
        return $KeyGenerator->Increment();
    }
    
    public $Id;
    public $Name;
    public $Description;
    public $CreatedDate;
    
    protected function CreateTableStructure(IColumnSet $Column) {
        $this->Id = $Column->IncrementInt32('Id');
        $this->Name = $Column->String('Name', 50);
        $this->Description = $Column->String('Description', 200);
        $this->CreatedDate = $Column->DateTime('CreatedDate');
    }
    
    public $Posts;

    protected function CreateRelations(Database $Database) {
        $this->Posts = new Relations\ToManyRelation($Database->Posts->BlogForeignKey);
    }

    protected function CreateRelationalStructure(Database $Database) {
        
    }
}

?>
