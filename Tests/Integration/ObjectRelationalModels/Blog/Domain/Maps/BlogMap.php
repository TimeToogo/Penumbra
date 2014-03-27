<?php

namespace Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Domain\Maps;

use \Penumbra\Core\Object\Domain;
use \Penumbra\Drivers\Base\Object;
use \Penumbra\Drivers\Base\Object\Properties;
use \Penumbra\Drivers\Base\Object\Properties\Accessors;
use \Penumbra\Drivers\Constant\Object\EntityMap;
use \Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Entities\Blog;

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
                \Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Entities\Post::GetType(), 
                new Properties\Relationships\Identifying(), 
                $Domain->PostMap->Blog);
    }

}

?>
