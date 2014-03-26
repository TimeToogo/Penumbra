<?php

namespace Penumbra\Api;

use \Penumbra\Drivers\Base\Relational\Queries\IConnection;
use \Penumbra\Drivers\Base\Object\Properties\Proxies\IProxyGenerator;
use \Penumbra\Pinq\Functional;
use \Penumbra\Utilities\Cache\ICache;

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
                new Functional\PHPParser\Parser(), 
                $Cache);
    }
}

?>
