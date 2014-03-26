<?php

namespace PenumbraExamples\One\Relational\Tables;

use \Penumbra\Core\Relational\Database;
use \Penumbra\Drivers\Constant\Relational;
use \Penumbra\Drivers\Base\Relational\Columns\IColumnSet;
use \Penumbra\Drivers\Base\Relational\PrimaryKeys\IKeyGeneratorSet;
use \Penumbra\Drivers\Base\Relational\Relations;

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
