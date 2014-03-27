<?php

namespace Penumbra\Api\Caching;

use \Penumbra\Api\Base;
use \Penumbra\Core\Mapping;
use \Penumbra\Drivers\Base\Relational\Queries\IConnection;
use \Penumbra\Drivers\Base\Object\Properties\Proxies\IProxyGenerator;
use \Penumbra\Pinq\Functional;
use \Penumbra\Utilities\Cache;

/**
 * This class provides a caching to an instance of DomainDatabaseMap, which can be very
 * expensive to instantiate.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ORM extends Base\ORM {
    const DomainDatabaseMapInstanceKey = 'DomainDatabaseMap';
    
    /**
     * The supplied cache.
     * 
     * @var Cache\ICache
     */
    private $Cache;
    
    public function __construct(
            callable $DomainDatabaseMapFactory,
            IConnection $Connection,
            IProxyGenerator $ProxyGenerator,
            Functional\IParser $FunctionParser,
            Cache\ICache $Cache) {
        $this->Cache = $Cache;
        
        $DomainDatabaseMap = $this->Cache->Retrieve(self::DomainDatabaseMapInstanceKey);
        
        if(!($DomainDatabaseMap instanceof \Penumbra\Core\Mapping\DomainDatabaseMap)) {
            $DomainDatabaseMap = $DomainDatabaseMapFactory();
            $this->Cache->Save(self::DomainDatabaseMapInstanceKey, $DomainDatabaseMap);
        }
        
        parent::__construct(
                $DomainDatabaseMap,
                $Connection,
                $ProxyGenerator,
                $FunctionParser);
    }
    
    protected function GetFunctionToExpressionTreeConverter(Functional\IParser $FunctionParser) {
        return new Functional\CachingFunctionToExpressionTreeConverter($this->Cache, $FunctionParser);
    }
}

?>
