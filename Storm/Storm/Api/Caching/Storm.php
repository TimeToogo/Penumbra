<?php

namespace Storm\Api\Caching;

use \Storm\Api\Base;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Object\Properties\Proxies\IProxyGenerator;
use \Storm\Pinq\Functional;
use \Storm\Utilities\Cache;
use \Storm\Pinq\CachingFunctionToExpressionTreeConverter;

/**
 * This class provides a caching to an instance of DomainDatabaseMap, which can be very
 * expensive to instantiate.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Storm extends Base\Storm {
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
        
        if(!($DomainDatabaseMap instanceof \Storm\Core\Mapping\DomainDatabaseMap)) {
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
        return new CachingFunctionToExpressionTreeConverter($this->Cache, $FunctionParser);
    }
}

?>
