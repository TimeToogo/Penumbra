<?php

namespace PenumbraExamples\One\Domain\Maps;

use \Penumbra\Core\Object\Domain;
use \Penumbra\Drivers\Base\Object;
use \Penumbra\Drivers\Base\Object\Properties;
use \Penumbra\Drivers\Base\Object\Properties\Accessors;
use \Penumbra\Drivers\Constant\Object\EntityMap;
use \PenumbraExamples\One\Entities\Tag;

class TagMap extends EntityMap {
    protected function EntityType() {
        return Tag::GetType();
    }
    
    protected function EntityConstructor() {
        return new Object\Construction\EmptyConstructor();
    }
    
    public $Id;
    public $Name;
    public $Description;
    public $Number;
    
    protected function CreateProperties(Domain $Domain) {
        $this->Id = new Properties\DataProperty(new Accessors\Field('Id'), true);
        $this->Name = new Properties\DataProperty(new Accessors\Field('Name'));
        $this->Description = new Properties\DataProperty(new Accessors\Field('Description'));
        $this->Number = new Properties\DataProperty(new Accessors\Field('Number'));
    }

}

?>
