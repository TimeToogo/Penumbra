<?php

namespace StormTests\One\Domain\Maps;

use \Storm\Drivers\Base\Object;
use \Storm\Drivers\Constant\Object\EntityMap;
use \StormTests\One\Entities\Blog;

class BlogMap extends EntityMap {
    protected function EntityType() {
        return Blog::GetType();
    }
    
    protected function EntityConstructor() {
        return new Object\Construction\EmptyConstructor();
    }
    
    public $Id;
    public $Name;
    public $Description;
    public $CreatedDate;
    public $Posts;
    
    protected function CreateProperties() {
        $this->Id = new Object\Properties\FieldProperty('Id', true);
        $this->Name = new Object\Properties\FieldProperty('Name');
        $this->Description = new Object\Properties\FieldProperty('Description');
        $this->CreatedDate = new Object\Properties\FieldProperty('CreatedDate');
        $this->Posts = new Object\Properties\FieldProperty('Posts');
    }

}

?>
