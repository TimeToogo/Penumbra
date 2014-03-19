<?php

namespace Storm\Tests\Integration\ObjectRelationalModels\Blog\Mapping;

use \Storm\Tests\Integration\ObjectRelationalModels\Base;

class BloggingDomainDatabaseMap extends Base\DomainDatabaseMap {
    public function __construct() {
        parent::__construct(\StormExamples\One\Example::GetPlatform());
    }
    
    protected function Domain() {
        return new \Storm\Tests\Integration\ObjectRelationalModels\Blog\Domain\BloggingDomain();
    }
    
    protected function LoadDatabase(\Storm\Drivers\Base\Relational\IPlatform $Platform) {
        return new \Storm\Tests\Integration\ObjectRelationalModels\Blog\Relational\BloggingDatabase($Platform);
    }
        
    public $BlogRelationalMap;
    public $AuthorRelationalMap;
    public $PostRelationalMap;
    public $TagRelationalMap;

    protected function CreateRelationalMaps() {
        $this->BlogRelationalMap = new Maps\BlogRelationalMap();
        $this->AuthorRelationalMap = new Maps\AuthorRelationalMap();
        $this->PostRelationalMap = new Maps\PostRelationalMap();
        $this->TagRelationalMap = new Maps\TagRelationalMap();
    }
}

?>
