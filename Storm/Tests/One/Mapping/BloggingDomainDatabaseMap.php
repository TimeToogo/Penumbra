<?php

namespace StormTests\One\Mapping;

use \Storm\Drivers\Base\Mapping\MappingConfiguration;
use \Storm\Drivers\Constant\Mapping;

class BloggingDomainDatabaseMap extends Mapping\DomainDatabaseMap {
    protected function Domain() {
        return new \StormTests\One\Domain\BloggingDomain();
    }
    
    protected function Database() {
        return new \StormTests\One\Relational\BloggingDatabase();
    }
    
    protected function MappingConfiguration() {
        return new MappingConfiguration(
                Mapping\LoadingMode::Lazy, 
                new \Storm\Drivers\Base\Mapping\Proxy\DevelopmentProxyGenerator
                        (__NAMESPACE__ . '\\' . 'Proxies', 
                        str_replace('\\', DIRECTORY_SEPARATOR, __DIR__) . DIRECTORY_SEPARATOR . 'Proxies'));
    }
    
    public $BlogRelationalMap;
    public $PostRelationalMap;
    public $TagRelationalMap;

    protected function CreateRelationalMaps() {
        $this->BlogRelationalMap = new Maps\BlogRelationalMap();
        $this->PostRelationalMap = new Maps\PostRelationalMap();
        $this->TagRelationalMap = new Maps\TagRelationalMap();
    }

}

?>
