<?php

namespace StormExamples\One\Domain\Maps;

use \Storm\Core\Object\Domain;
use \Storm\Drivers\Base\Object;
use \Storm\Drivers\Base\Object\Properties;
use \Storm\Drivers\Base\Object\Properties\Accessors;
use \Storm\Drivers\Constant\Object\EntityMap;
use \StormExamples\One\Entities\Author;

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
                \StormExamples\One\Entities\Profile::GetType(), 
                new Properties\Relationships\Identifying(),
                false,
                false,
                $Domain->ProfileMap->Author);
        
        $this->Friends = new Properties\CollectionProperty(new Accessors\Field('Friends'), 
                \StormExamples\One\Entities\Author::GetType(),
                new Properties\Relationships\CascadeNonIdentifying());
    }

}

?>
