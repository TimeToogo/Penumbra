<?php

namespace Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Domain;

use \Penumbra\Drivers\Constant\Object;

class BloggingDomain extends Object\Domain {
    public $TagMap;
    public $PostMap;
    public $AuthorMap;
    public $BlogMap;
    
    protected function CreateEntityMaps() {
        $this->TagMap = new Maps\TagMap();
        $this->PostMap = new Maps\PostMap();
        $this->AuthorMap = new Maps\AuthorMap();
        $this->BlogMap = new Maps\BlogMap();
    }
}

?>