<?php

namespace Penumbra\Api;

use \Penumbra\Pinq\Functional;
use \Penumbra\Drivers\Base\Relational\Queries\IConnection;
use \Penumbra\Drivers\Base\Object\Properties\Proxies\IProxyGenerator;
use \Penumbra\Utilities\Cache\ICache;

/**
 * This configuration interface provides the required 
 * components to the the penumbra instance aswell as providing
 * a facade for constructing the Penumbra instance.
 * This is the entry point to a Penumbra application.
 * 
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IConfiguration {
    
    /**
     * @return static
     */
    public function SetDomainDatabaseMapFactory(callable $DomainDatabaseMapFactory);
    
    /**
     * @return static
     */
    public function SetConnection(IConnection $Connection);
    
    /**
     * @return static
     */
    public function SetProxyGenerator(IProxyGenerator $ProxyGenerator);
    
    /**
     * @return static
     */
    public function SetFunctionParser(Functional\IParser $FunctionParser);
        
    /**
     * @return static
     */
    public function SetCache(ICache $Cache = null);
    
    /**
     * @return Base\ORM
     */
    public function BuildORM();
}

?>
