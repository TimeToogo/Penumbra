<?php

namespace Storm\Api\Base;

use \Storm\Core\Object;
use \Storm\Utilities\Cache\ICache;

/**
 * The Repository provides the clean api for querying on a specific
 * type of entity.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class IdentityMap {
    
    /**
     * The EntityMap for this identity map.
     * 
     * @var Object\EntityMap
     */
    protected $EntityMap;
    
    /**
     * The cache to use as the identity map for the repository
     * 
     * @var ICache 
     */
    private $Cache;
    
    public function __construct(Object\EntityMap $EntityMap, ICache $Cache) {
        $this->EntityMap = $EntityMap;
        $this->Cache = $Cache;
    }
        
    final public function GetFromCache(array $Identity) {
        $IdentityHash = md5(json_encode($Identity));

        return $this->Cache->Contains($IdentityHash) ?
                $this->Cache->Retrieve($IdentityHash) : null;
    }

    final public function RemoveAllFromCache(array $Entities) {
        array_walk($Entities, [$this, 'RemoveFromCache']);
    }

    final public function RemoveFromCache($Entity) {
        $IdentityHash = $this->EntityMap->Identity($Entity)->Hash();

        $this->Cache->Delete($IdentityHash);
    }

    final public function CacheEntities(array $Entities) {
        array_map([$this, 'CacheEntity'], $Entities);
    }

    final public function CacheEntity($Entity, array $Identity = null) {
        if($Identity === null && !$this->EntityMap->HasIdentity($Entity)) {
            return;
        }
        $Identity = $Identity ?: $this->EntityMap->Identity($Entity);
        $IdentityHash = md5(json_encode($Identity));
        $this->Cache->Save($IdentityHash, $Entity);
    }

    final public function Clear() {
        $this->Cache->Clear();
    }
}

?>