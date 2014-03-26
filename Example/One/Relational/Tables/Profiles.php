<?php

namespace StormExamples\One\Relational\Tables;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational\Database;
use \Storm\Drivers\Constant\Relational;
use \Storm\Drivers\Base\Relational\Columns\IColumnSet;
use \Storm\Drivers\Base\Relational\PrimaryKeys\IKeyGeneratorSet;
use \Storm\Drivers\Base\Relational\Traits;
use \Storm\Drivers\Base\Relational\Relations;

class Profiles extends Relational\Table {
    protected function Name() {
        return 'Profiles';
    }
    
    protected function KeyGenerator(IKeyGeneratorSet $KeyGenerator) {
        return $KeyGenerator->Increment();
    }
    
    public $AuthorId;
    public $DateOfBirth;
    public $Description;
    public $Location;
    
    protected function CreateTableStructure(IColumnSet $Column) {
        $this->AuthorId = $Column->IncrementInt32('AuthorId');
        $this->DateOfBirth = $Column->DateTime('DateOfBirth');
        $this->Description = $Column->String('Description', 500);
        $this->Location = $Column->String('Location', 50);
    }
    
    public $AuthorForeignKey;

    protected function CreateRelationalStructure(Database $Database) {
        $this->AuthorForeignKey = new Traits\ForeignKey('AuthorForeignKey', 
                Map::From([$this->AuthorId], [$Database->Authors->Id]),
                Traits\ForeignKeyMode::Cascade, Traits\ForeignKeyMode::Cascade);
    }
    
    public $Author;
    
    protected function CreateRelations(Database $Database) {
        $this->Author = new Relations\ToOneRelation($this->AuthorForeignKey);
    }
}

?>
