<?php

namespace Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Domain\Maps;

use \Penumbra\Core\Object\Domain;
use \Penumbra\Drivers\Base\Object;
use \Penumbra\Drivers\Base\Object\Properties;
use \Penumbra\Drivers\Base\Object\Properties\Accessors;
use \Penumbra\Drivers\Constant\Object\EntityMap;
use \Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Entities\Post;

class PostMap extends EntityMap {
    protected function EntityType() {
        return Post::GetType();
    }
    
    protected function EntityConstructor() {
        return new Object\Construction\EmptyConstructor();
    }
    
    public $Title;
    public $Content;
    public $CreatedDate;
    public $Blog;
    public $Author;
    public $Tags;
    
    protected function CreateProperties(Domain $Domain) {
        $this->Title = new Properties\DataProperty(new Accessors\Field('Title'), true);
        $this->Content = new Properties\DataProperty(new Accessors\Field('Content'));
        $this->CreatedDate = new Properties\DataProperty(new Accessors\Field('CreatedDate'));
        
        $this->Blog = new Properties\DataProperty(new Accessors\Field('Blog'));
        
        $this->Author = new Properties\EntityProperty(new Accessors\Field('Author'), 
                \Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Entities\Author::GetType(), 
                new Properties\Relationships\CascadeNonIdentifying());
        
        $this->Tags = new Properties\CollectionProperty(new Accessors\Field('Tags'), 
                \Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Entities\Tag::GetType(),
                new Properties\Relationships\CascadeNonIdentifying());
    }

}

?>
