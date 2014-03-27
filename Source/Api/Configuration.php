<?php

namespace Penumbra\Api;

use \Penumbra\Core\Mapping\DomainDatabaseMap;
use \Penumbra\Drivers\Base\Relational\Queries\IConnection;
use \Penumbra\Drivers\Base\Object\Properties\Proxies\IProxyGenerator;
use \Penumbra\Pinq\Functional;
use \Penumbra\Utilities\Cache\ICache;

/**
 * This configuration class serves as the configuration for
 * the penumbra api.
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
     * @var Functional\IParser
     */
    private $FunctionParser;
    
    /**
     * @var ICache|null 
     */
    private $Cache;
    
    public function __construct(
            callable $DomainDatabaseMapFactory, 
            IConnection $Connection,
            IProxyGenerator $ProxyGenerator,
            Functional\IParser $ClosureParser, 
            ICache $Cache = null) {
        $this->DomainDatabaseMapFactory = $DomainDatabaseMapFactory;
        $this->ProxyGenerator = $ProxyGenerator;
        $this->Connection = $Connection;
        $this->FunctionParser = $ClosureParser;
        $this->Cache = $Cache;
    }
    
    public function BuildORM() {
        if($this->Cache === null) {
            $Factory = $this->DomainDatabaseMapFactory;
            return new Base\ORM(
                    $Factory(), 
                    $this->Connection, 
                    $this->ProxyGenerator,
                    $this->FunctionParser);
        }
        else {
            return new Caching\ORM(
                    $this->DomainDatabaseMapFactory,
                    $this->Connection, 
                    $this->ProxyGenerator,
                    $this->FunctionParser,
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
    
    final public function SetFunctionParser(Functional\IParser $ClosureParser) {
        $this->FunctionParser = $ClosureParser;
        return $this;
    }
    
    final public function SetCache(ICache $Cache = null) {
        $this->Cache = $Cache;
        return $this;
    }
}

?>
