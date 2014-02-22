<?php

namespace Storm\Api;

use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Object\Properties\Proxies\IProxyGenerator;
use \Storm\Drivers\Fluent\Object\Functional;
use \Storm\Utilities\Cache\ICache;

/**
 * This configuration class serves as the configuration for
 * the storm api.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Configuration implements IConfiguration {
    
    /**
     * @var callable 
     */
    private $DomainDatabaseMapFactory;
    
    /**
     * @var IConnection 
     */
    private $Connection;
    
    /**
     * @var IConnection 
     */
    private $ProxyGenerator;
    
    /**
     * @var Closure\IReader
     */
    private $ClosureReader;
    
    /**
     * @var Closure\IParser
     */
    private $ClosureParser;
    
    /**
     * @var ICache|null 
     */
    private $Cache;
    
    public function __construct(
            callable $DomainDatabaseMapFactory, 
            IConnection $Connection,
            IProxyGenerator $ProxyGenerator,
            Functional\IReader $ClosureReader, 
            Functional\IParser $ClosureParser, 
            ICache $Cache = null) {
        $this->DomainDatabaseMapFactory = $DomainDatabaseMapFactory;
        $this->ProxyGenerator = $ProxyGenerator;
        $this->Connection = $Connection;
        $this->ClosureReader = $ClosureReader;
        $this->ClosureParser = $ClosureParser;
        $this->Cache = $Cache;
    }
    
    public function Storm() {
        if($this->Cache === null) {
            $Factory = $this->DomainDatabaseMapFactory;
            return new Base\Storm(
                    $Factory(), 
                    $this->Connection, 
                    $this->ProxyGenerator,
                    $this->ClosureReader, 
                    $this->ClosureParser);
        }
        else {
            return new Caching\Storm(
                    $this->DomainDatabaseMapFactory,
                    $this->Connection, 
                    $this->ProxyGenerator,
                    $this->ClosureReader, 
                    $this->ClosureParser,
                    $this->Cache);
        }
    }
    
    final public function SetDomainDatabaseMapFactory(callable $DomainDatabaseMapFactory) {
        $this->DomainDatabaseMapFactory = $DomainDatabaseMapFactory;
        return $this;
    }
    
    public function SetConnection(IConnection $Connection) {
        $this->Connection = $Connection;
        return $this;
    }
    
    public function SetProxyGenerator(IProxyGenerator $ProxyGenerator) {
        $this->ProxyGenerator = $ProxyGenerator;
        return $this;
    }
    
    final public function SetFunctionReader(Functional\IReader $ClosureReader) {
        $this->ClosureReader = $ClosureReader;
        return $this;
    }

    final public function SetFunctionParser(Functional\IParser $ClosureParser) {
        $this->ClosureParser = $ClosureParser;
        return $this;
    }
    
    final public function SetCache(ICache $Cache = null) {
        $this->Cache = $Cache;
        return $this;
    }
}

?>
