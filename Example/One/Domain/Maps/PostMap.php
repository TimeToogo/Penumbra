<?php

namespace StormExamples\One\Domain\Maps;

use \Storm\Core\Object\Domain;
use \Storm\Drivers\Base\Object;
use \Storm\Drivers\Base\Object\Properties;
use \Storm\Drivers\Base\Object\Properties\Accessors;
use \Storm\Drivers\Constant\Object\EntityMap;
use \StormExamples\One\Entities\Post;

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
    public $Tags;
    
    protected function CreateProperties(Domain $Domain) {
        $ProxyGenerator = new Properties\Proxies\DevelopmentProxyGenerator(
                __NAMESPACE__ . '\\' . 'Proxies', 
                str_replace('\\', DIRECTORY_SEPARATOR, __DIR__) . DIRECTORY_SEPARATOR . 'Proxies');
        
        
        $this->Title = new Properties\DataProperty(new Accessors\Field('Title'), true);
        $this->Content = new Properties\DataProperty(new Accessors\Field('Content'));
        $this->CreatedDate = new Properties\DataProperty(new Accessors\Field('CreatedDate'));
        
        $this->Blog = new Properties\DataProperty(new Accessors\Field('Blog'));
        
        $this->Tags = new Properties\CollectionProperty(new Accessors\Field('Tags'), 
                \StormExamples\One\Entities\Tag::GetType(),
                new Properties\Relationships\CascadeNonIdentifying(), 
                null,
                $ProxyGenerator);
    }

}

?>
