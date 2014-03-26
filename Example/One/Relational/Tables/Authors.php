<?php

namespace PenumbraExamples\One\Relational\Tables;

use \Penumbra\Core\Relational\Database;
use \Penumbra\Drivers\Constant\Relational;
use \Penumbra\Drivers\Base\Relational\Columns\IColumnSet;
use \Penumbra\Drivers\Base\Relational\PrimaryKeys\IKeyGeneratorSet;
use \Penumbra\Drivers\Base\Relational\Traits;
use \Penumbra\Drivers\Base\Relational\Relations;


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
    
    public $Profile;
    public $Friends;
    
    protected function CreateRelations(Database $Database) {
        $this->Profile = new Relations\InversedToOneRelation($Database->Profiles->AuthorForeignKey);
        $this->Friends = new Relations\JoinTableRelation($Database->AuthorFriends);
    }
}

?>
