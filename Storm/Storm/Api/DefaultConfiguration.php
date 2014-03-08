<?php

namespace Storm\Api;

use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Object\Properties\Proxies\IProxyGenerator;
use \Storm\Drivers\Fluent\Object\Functional;
use \Storm\Utilities\Cache\ICache;

/**
 * This configuration class provides defaults for the
 * configuration class
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class DefaultConfiguration extends Configuration {
    public function __construct(
            callable $DomainDatabaseMapFactory,
            IConnection $Connection, 
            IProxyGenerator $ProxyGenerator,
            ICache $Cache = null) {
        parent::__construct(
                $DomainDatabaseMapFactory, 
                $Connection,
                $ProxyGenerator,
                new Functional\Implementation\PHPParser\Parser(), 
                $Cache);
    }
}

?>
