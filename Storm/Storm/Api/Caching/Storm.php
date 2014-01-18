<?php

namespace Storm\Api\Caching;

use \Storm\Api\Wrapper;
use \Storm\Api\Base;
use \Storm\Utilities\Cache;
use \Storm\Drivers\Base\Relational\IPlatform;

/**
 * This class provides a caching to an instance of DomainDatabaseMap, which can be very
 * expensive to fully instantiate.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Storm extends Base\Storm {
    const StormInstanceKey = 'Storm';
    
    /**
     * The supplied cache.
     * 
     * @var Cache\ICache
     */
    private $Cache;
    
    /**
     * The amount of time an entity should remain cached after retreival.
     * 
     * @var int 
     */
    private $EntityExpirySeconds;
    
    public function __construct(
            Cache\ICache $Cache, 
            IPlatform $Platform, 
            callable $StormConstructor,
            $EntityExpirySeconds = 300) {
        $this->Cache = $Cache;
        $this->EntityExpirySeconds = $EntityExpirySeconds;
        
        $Storm = $this->Cache->Retrieve(self::StormInstanceKey);
        
        if(!($Storm instanceof \Storm\Api\Base\Storm)) {
            $Storm = $StormConstructor();
            $this->Cache->Save(self::StormInstanceKey, $Storm);
        }
        
        $Storm->GetDomainDatabaseMap()->GetDatabase()->SetPlatform($Platform);
        
        parent::__construct($Storm->GetDomainDatabaseMap());
    }
    
    protected function ConstructRepository($EntityType, $AutoSave = false) {
        return new Repository(
                $this->GetDomainDatabaseMap(),
                $EntityType, 
                $AutoSave,
                $this->Cache,
                new Cache\DevelopmentCache(),
                $this->EntityExpirySeconds);
    }
}

?>
