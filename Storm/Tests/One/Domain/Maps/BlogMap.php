<?php

namespace StormTests\One\Domain\Maps;

use \Storm\Drivers\Base\Object;
use \Storm\Drivers\Base\Object\Properties;
use \Storm\Drivers\Base\Object\Properties\Accessors;
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
        $this->Id = new Properties\DataProperty(new Accessors\Field('Id'), true);
        $this->Name = new Properties\DataProperty(new Accessors\Field('Name'));
        $this->Description = new Properties\DataProperty(new Accessors\Field('Description'));
        $this->CreatedDate = new Properties\DataProperty(new Accessors\Field('CreatedDate'));
        $this->Posts = new Properties\CollectionProperty(new Accessors\Field('Posts'), \StormTests\One\Entities\Post::GetType(), true);
    }

}

?>
