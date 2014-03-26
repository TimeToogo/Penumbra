<?php

namespace PenumbraExamples\One\Mapping;

use \Penumbra\Drivers\Constant\Mapping;

class BloggingDomainDatabaseMap extends Mapping\DomainDatabaseMap {
    public function __construct() {
        parent::__construct(\PenumbraExamples\One\Example::GetPlatform());
    }
    
    protected function Domain() {
        return new \PenumbraExamples\One\Domain\BloggingDomain();
    }
    
    protected function Database() {
        return new \PenumbraExamples\One\Relational\BloggingDatabase();
    }
        
    public $BlogRelationalMap;
    public $AuthorRelationalMap;
    public $ProfileRelationalMap;
    public $PostRelationalMap;
    public $TagRelationalMap;

    protected function CreateRelationalMaps() {
        $this->BlogRelationalMap = new Maps\BlogRelationalMap();
        $this->AuthorRelationalMap = new Maps\AuthorRelationalMap();
        $this->ProfileRelationalMap = new Maps\ProfileRelationalMap();
        $this->PostRelationalMap = new Maps\PostRelationalMap();
        $this->TagRelationalMap = new Maps\TagRelationalMap();
    }
}

?>
