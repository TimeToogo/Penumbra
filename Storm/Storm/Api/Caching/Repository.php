<?php

namespace Storm\Api\Caching;

use \Storm\Api\Wrapper;
use \Storm\Api\Base;
use \Storm\Core\Object;
use \Storm\Utilities\Cache\ICache;
use \Storm\Core\Mapping\DomainDatabaseMap;

/**
 * This class provides a caching layer over a repository, reducing unnessecary queries.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Repository extends Base\Repository {
    private $Cache;
    private $EntityCache;
    private $EntityExpirySeconds;
    
    public function __construct(
            DomainDatabaseMap $DomainDatabaseMap, 
            $EntityType, 
            $AutoSave,
            ICache $Cache, 
            ICache $EntityCache, 
            $EntityExpirySeconds) {
        parent::__construct($DomainDatabaseMap, $EntityType, $AutoSave);
        $this->Cache = $Cache;
        $this->EntityCache = $EntityCache;
        $this->EntityExpirySeconds = $EntityExpirySeconds;    
    }
    
    protected function GetClosureToASTConverter() {
        return new ClosureToASTConverter($this->Cache, parent::GetClosureToASTConverter());
    }
    
    public function LoadRequest(Object\IRequest $Request) {
        $Entities = parent::LoadRequest($Request);
        
        if(is_array($Entities)) {
            $this->CacheEntities($Entities);
        }
        else if ($Entities instanceof $this->EntityType) {
            $this->CacheEntity($Entities);
        }
        
        return $Entities;
    }
    
    protected function LoadByIdentity(Object\Identity $Identity) {
        $CachedEntity = $this->GetFromCache($Identity);
        if($CachedEntity instanceof $this->EntityType) {
            return $CachedEntity;
        }
        
        $Entity = parent::LoadByIdentity($Identity);
        if($Entity instanceof $this->EntityType) {
            $this->CacheEntity($Entity, $Identity);
        }
        
        return $Entity;
    }
    
    public function Persist($Entity) {
        $this->CacheEntity($Entity);
        
        return parent::Persist($Entity);
    }
    
    public function PersistAll(array $Entities) {
        $this->CacheEntities($Entities);
        
        parent::PersistAll($Entities);
    }
    public function Discard($EntityOrCriterion) {
        parent::Discard($EntityOrCriterion);
        if($EntityOrCriterion instanceof $this->EntityType) {
            $this->RemoveFromCache($EntityOrCriterion);
        }
    }
    
    public function DiscardAll(array $Entities) {
        $this->RemoveAllFromCache($Entities);
        
        parent::DiscardAll($Entities);
    }
    
    // <editor-fold defaultstate="collapsed" desc="Caching methods">
    
    private function GetFromCache(Object\Identity $Identity) {
        $IdentityHash = $Identity->Hash();

        return $this->Cache->Contains($IdentityHash) ?
                $this->Cache->Retrieve($IdentityHash) : null;
    }

    private function RemoveAllFromCache(array $Entities) {
        array_walk($Entities, [$this, 'RemoveFromCache']);
    }

    private function RemoveFromCache($Entity) {
        $IdentityHash = $this->EntityMap->Identity($Entity)->Hash();

        $this->Cache->Delete($IdentityHash);
    }

    private function CacheEntities(array $Entities) {
        array_walk($Entities, [$this, 'CacheEntity']);
    }

    private function CacheEntity($Entity, Object\Identity $Identity = null) {
        $Identity = $Identity ? : $this->EntityMap->Identity($Entity);
        $IdentityHash = $Identity->Hash();
        $this->EntityCache->Save($IdentityHash, $Entity, $this->EntityExpirySeconds);
    }

    // </editor-fold>
}

?>