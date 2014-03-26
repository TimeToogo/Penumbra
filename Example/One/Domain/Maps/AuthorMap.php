<?php

namespace PenumbraExamples\One\Domain\Maps;

use \Penumbra\Core\Object\Domain;
use \Penumbra\Drivers\Base\Object;
use \Penumbra\Drivers\Base\Object\Properties;
use \Penumbra\Drivers\Base\Object\Properties\Accessors;
use \Penumbra\Drivers\Constant\Object\EntityMap;
use \PenumbraExamples\One\Entities\Author;

class AuthorMap extends EntityMap {
    protected function EntityType() {
        return Author::GetType();
    }
    
    protected function EntityConstructor() {
        return new Object\Construction\EmptyConstructor();
    }
    
    public $Id;
    public $FirstName;
    public $LastName;
    public $Profile;
    public $Friends;
    
    protected function CreateProperties(Domain $Domain) {
        $this->Id = new Properties\DataProperty(new Accessors\Field('Id'), true);
        $this->FirstName = new Properties\DataProperty(new Accessors\Field('FirstName'));
        $this->LastName = new Properties\DataProperty(new Accessors\Field('LastName'));
        
        $this->Profile = new Properties\EntityProperty(new Accessors\Field('Profile'), 
                \PenumbraExamples\One\Entities\Profile::GetType(), 
                new Properties\Relationships\Identifying(),
                false,
                false,
                $Domain->ProfileMap->Author);
        
        $this->Friends = new Properties\CollectionProperty(new Accessors\Field('Friends'), 
                \PenumbraExamples\One\Entities\Author::GetType(),
                new Properties\Relationships\CascadeNonIdentifying());
    }

}

?>
