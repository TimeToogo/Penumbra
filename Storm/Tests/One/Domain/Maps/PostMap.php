<?php

namespace StormTests\One\Domain\Maps;

use \Storm\Drivers\Base\Object;
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
        $this->Blog = new Object\Properties\FieldProperty('Blog');
        $this->BlogId = new Object\Properties\TraversedObjectProperty([$this->Blog], 
                new Object\Properties\GetterSetter('BlogId', true, new Object\Properties\GetterField('Id')));
        
        $this->Title = new Object\Properties\FieldProperty('Title', true);
        $this->Content = new Object\Properties\FieldProperty('Content');
        $this->CreatedDate = new Object\Properties\FieldProperty('CreatedDate');
        
        $this->Tags = new Object\Properties\FieldProperty('Tags');
    }

}

?>
