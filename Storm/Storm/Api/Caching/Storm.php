<?php

namespace Storm\Api\Caching;

use \Storm\Api\Base;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Drivers\Fluent\Object\Closure;
use \Storm\Utilities\Cache;
use \Storm\Drivers\Base\Relational\IPlatform;

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
            IPlatform $Platform, 
            callable $DomainDatabaseMapFactory,
            Closure\IReader $ClosureReader, 
            Closure\IParser $ClosureParser,
            Cache\ICache $Cache) {
        $this->Cache = $Cache;
        
        $DomainDatabaseMap = $this->Cache->Retrieve(self::DomainDatabaseMapInstanceKey);
        
        if(!($DomainDatabaseMap instanceof \Storm\Core\Mapping\DomainDatabaseMap)) {
            $DomainDatabaseMap = $DomainDatabaseMapFactory();
            $this->Cache->Save(self::DomainDatabaseMapInstanceKey, $DomainDatabaseMap);
        }
        
        $DomainDatabaseMap->GetDatabase()->SetPlatform($Platform);
        
        parent::__construct($DomainDatabaseMap, $ClosureReader, $ClosureParser);
    }
    
    protected function GetClosureToASTConverter(Closure\IReader $ClosureReader, Closure\IParser $ClosureParser) {
        return new ClosureToASTConverter($this->Cache, $ClosureReader, $ClosureParser);
    }
}

?>
