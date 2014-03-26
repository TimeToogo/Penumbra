<?php

namespace PenumbraExamples\One\Relational\Tables;

use \Penumbra\Core\Containers\Map;
use \Penumbra\Core\Relational\Database;
use \Penumbra\Drivers\Constant\Relational;
use \Penumbra\Drivers\Base\Relational\Columns\IColumnSet;
use \Penumbra\Drivers\Base\Relational\PrimaryKeys\IKeyGeneratorSet;
use \Penumbra\Drivers\Base\Relational\Traits;
use \Penumbra\Drivers\Base\Relational\Relations;

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
