<?php

namespace StormExamples\One\Mapping;

use \Storm\Drivers\Base\Mapping\MappingConfiguration;
use \Storm\Drivers\Constant\Mapping;

class BloggingDomainDatabaseMap extends Mapping\DomainDatabaseMap {
    protected function Domain() {
        return new \StormExamples\One\Domain\BloggingDomain();
    }
    
    protected function Database() {
        return new \StormExamples\One\Relational\BloggingDatabase();
    }
    
    protected function MappingConfiguration() {
        return new MappingConfiguration(
                Mapping\LoadingMode::Lazy
                );
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
