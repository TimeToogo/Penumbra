<?php

namespace PenumbraExamples\One\Domain\Maps;

use \Penumbra\Core\Object\Domain;
use \Penumbra\Drivers\Base\Object;
use \Penumbra\Drivers\Base\Object\Properties;
use \Penumbra\Drivers\Base\Object\Properties\Accessors;
use \Penumbra\Drivers\Constant\Object\EntityMap;
use \PenumbraExamples\One\Entities\Post;

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
        
        $this->Blog = new Properties\EntityProperty(new Accessors\Field('Blog'), 
                \PenumbraExamples\One\Entities\Blog::GetType(), 
                new Properties\Relationships\NonIdentifying(),
                false,
                true);
        
        $this->Author = new Properties\EntityProperty(new Accessors\Field('Author'), 
                \PenumbraExamples\One\Entities\Author::GetType(), 
                new Properties\Relationships\CascadeNonIdentifying());
        
        $this->Tags = new Properties\CollectionProperty(new Accessors\Field('Tags'), 
                \PenumbraExamples\One\Entities\Tag::GetType(),
                new Properties\Relationships\CascadeNonIdentifying());
    }

}

?>
