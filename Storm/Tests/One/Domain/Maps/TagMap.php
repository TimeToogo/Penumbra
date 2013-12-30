<?php

namespace StormTests\One\Domain\Maps;

use \Storm\Drivers\Base\Object;
use \Storm\Drivers\Base\Object\Properties;
use \Storm\Drivers\Base\Object\Properties\Accessors;
use \Storm\Drivers\Constant\Object\EntityMap;
use \StormTests\One\Entities\Tag;

class TagMap extends EntityMap {
    protected function EntityType() {
        return Tag::GetType();
    }
    
    protected function EntityConstructor() {
        return new Object\Construction\EmptyConstructor();
    }
    
    public $Id;
    public $Name;
    
    protected function CreateProperties() {
        $this->Id = new Properties\DataProperty(new Accessors\Field('Id'), true);
        $this->Name = new Properties\DataProperty(new Accessors\Field('Name'));
    }

}

?>
