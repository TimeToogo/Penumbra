<?php

namespace StormExamples\One\Domain\Maps;

use \Storm\Core\Object\Domain;
use \Storm\Drivers\Base\Object;
use \Storm\Drivers\Base\Object\Properties;
use \Storm\Drivers\Base\Object\Properties\Accessors;
use \Storm\Drivers\Constant\Object\EntityMap;
use \StormExamples\One\Entities\Profile;

class ProfileMap extends EntityMap {
    protected function EntityType() {
        return Profile::GetType();
    }
    
    protected function EntityConstructor() {
        return new Object\Construction\EmptyConstructor();
    }
    
    public $Author;
    public $AuthorId;
    public $DateOfBirth;
    public $Description;
    public $Location;
    
    protected function CreateProperties(Domain $Domain) {
        
        $this->Author = new Properties\EntityProperty(new Accessors\Field('Author'), 
                \StormExamples\One\Entities\Author::GetType(), 
                new Properties\Relationships\CascadeNonIdentifying(),
                false,
                true);
        
        $this->DateOfBirth = new Properties\DataProperty(new Accessors\Field('DateOfBirth'));
        $this->Description = new Properties\DataProperty(new Accessors\Field('Description'));
        $this->Location = new Properties\DataProperty(new Accessors\Field('Location'));
    }

}

?>