<?php

namespace PenumbraExamples\One\Domain;

use \Penumbra\Drivers\Constant\Object;

class BloggingDomain extends Object\Domain {
    public $TagMap;
    public $PostMap;
    public $AuthorMap;
    public $ProfileMap;
    public $BlogMap;
    
    protected function CreateEntityMaps() {
        $this->TagMap = new Maps\TagMap();
        $this->PostMap = new Maps\PostMap();
        $this->ProfileMap = new Maps\ProfileMap();
        $this->AuthorMap = new Maps\AuthorMap();
        $this->BlogMap = new Maps\BlogMap();
    }
}

?>