<?php

namespace Storm\Api;

use \Storm\Drivers\Fluent\Object\Functional;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Utilities\Cache\ICache;

/**
 * This configuration interface provides the required 
 * components to the the storm instance aswell as providing
 * a facade for constructing the Storm instance.
 * This is the entry point to a Storm application.
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
    public function SetFunctionReader(Functional\IReader $ClosureReader);
    
    /**
     * @return static
     */
    public function SetFunctionParser(Functional\IParser $ClosureParser);
        
    /**
     * @return static
     */
    public function SetCache(ICache $Cache = null);
    
    /**
     * @return Base\Storm
     */
    public function Storm();
}

?>
