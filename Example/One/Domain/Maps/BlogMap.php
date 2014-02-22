<?php

namespace StormExamples\One\Domain\Maps;

use \Storm\Core\Object\Domain;
use \Storm\Drivers\Base\Object;
use \Storm\Drivers\Base\Object\Properties;
use \Storm\Drivers\Base\Object\Properties\Accessors;
use \Storm\Drivers\Constant\Object\EntityMap;
use \StormExamples\One\Entities\Blog;

class BlogMap extends EntityMap {
    protected function EntityType() {
        return Blog::GetType();
    }
    
    protected function EntityConstructor() {
        return new Object\Construction\EmptyConstructor();
    }
    
    public $Id;
    public $Name;
    public $NameGetter;
    public $Description;
    public $CreatedDate;
    public $Posts;
    
    protected function CreateProperties(Domain $Domain) {
        $this->Id = new Properties\DataProperty(new Accessors\Field('Id'), true);
        $this->Name = new Properties\DataProperty(new Accessors\MethodPair('GetName', 'SetName'));
        $this->Description = new Properties\DataProperty(new Accessors\Field('Description'));
        $this->CreatedDate = new Properties\DataProperty(new Accessors\Field('CreatedDate'));
        
        $this->Posts = new Properties\CollectionProperty(new Accessors\Field('Posts'), 
                \StormExamples\One\Entities\Post::GetType(), 
                new Properties\Relationships\Identifying(), 
                $Domain->PostMap->Blog);
    }

}

?>
