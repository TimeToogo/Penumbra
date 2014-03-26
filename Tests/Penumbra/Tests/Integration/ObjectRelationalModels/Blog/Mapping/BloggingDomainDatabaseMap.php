<?php

namespace Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Mapping;

use \Penumbra\Tests\Integration\ObjectRelationalModels\Base;

class BloggingDomainDatabaseMap extends Base\DomainDatabaseMap {
    public function __construct() {
        parent::__construct(\PenumbraExamples\One\Example::GetPlatform());
    }
    
    protected function Domain() {
        return new \Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Domain\BloggingDomain();
    }
    
    protected function LoadDatabase(\Penumbra\Drivers\Base\Relational\IPlatform $Platform) {
        return new \Penumbra\Tests\Integration\ObjectRelationalModels\Blog\Relational\BloggingDatabase($Platform);
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
