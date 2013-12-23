<?php

namespace StormTests\One\Domain;

use \Storm\Drivers\Constant\Object;

class BloggingDomain extends Object\Domain {
    public $BlogMap;
    public $PostMap;
    public $TagMap;
    
    protected function CreateEntityMaps() {
        $this->BlogMap = new Maps\BlogMap();
        $this->PostMap = new Maps\PostMap();
        $this->TagMap = new Maps\TagMap();
    }
}

?>
