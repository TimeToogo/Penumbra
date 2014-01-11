<?php

namespace Storm\Api\Caching;

use \Storm\Api\Wrapper;
use \Storm\Utilities\Cache;
use \Storm\Drivers\Base\Relational\IPlatform;

class Storm extends Wrapper\Storm {
    const StormInstanceKey = 'Storm';
    private $Cache;
    
    public function __construct(Cache\ICache $Cache, IPlatform $Platform, callable $StormConstructor) {
        $this->Cache = $Cache;
        $Storm = $this->Cache->Retrieve(self::StormInstanceKey);
        
        if(!($Storm instanceof \Storm\Api\Base\Storm)) {
            $Storm = $StormConstructor();
            $this->Cache->Save(self::StormInstanceKey, $Storm);
        }
        
        $Storm->GetDomainDatabaseMap()->GetDatabase()->SetPlatform($Platform);
        
        parent::__construct($Storm);
    }
}

?>
