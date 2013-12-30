<?php

namespace StormTests\One\Domain\Maps;

use \Storm\Drivers\Base\Object;
use \Storm\Drivers\Base\Object\Properties;
use \Storm\Drivers\Base\Object\Properties\Accessors;
use \Storm\Drivers\Constant\Object\EntityMap;
use \StormTests\One\Entities\Post;

class PostMap extends EntityMap {
    protected function EntityType() {
        return Post::GetType();
    }
    
    protected function EntityConstructor() {
        return new Object\Construction\EmptyConstructor();
    }
    
    public $Blog;
    public $BlogId;
    public $Title;
    public $Content;
    public $CreatedDate;
    public $Tags;
    
    protected function CreateProperties() {
        $this->Blog = new Properties\EntityProperty(new Accessors\Field('Blog'), \StormTests\One\Entities\Blog::GetType());
        $this->BlogId = new Properties\DataProperty(new Accessors\Traversing([$this->Blog->GetAccessor(), new Accessors\Field('Id')]), true);
        
        $this->Title = new Properties\DataProperty(new Accessors\Field('Title'), true);
        $this->Content = new Properties\DataProperty(new Accessors\Field('Content'));
        $this->CreatedDate = new Properties\DataProperty(new Accessors\Field('CreatedDate'));
        
        $this->Tags = new Properties\CollectionProperty(new Accessors\Field('Tags'), \StormTests\One\Entities\Tag::GetType());
    }

}

?>
