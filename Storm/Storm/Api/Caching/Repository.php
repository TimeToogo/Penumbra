<?php

namespace Storm\Api\Caching;

use \Storm\Api\Wrapper;
use \Storm\Api\Base;
use \Storm\Core\Object;
use \Storm\Utilities\Cache\ICache;

class Repository extends Wrapper\Repository {
    private $Cache;
    private $EntityCache;
    private $EntityExpirySeconds;
    
    public function __construct(
            ICache $Cache, 
            ICache $EntityCache, 
            $EntityExpirySeconds,
            Base\Repository $Repository) {
        parent::__construct($Repository);
        
        $this->Cache = $Cache;
        $this->EntityCache = $EntityCache;
        $this->EntityExpirySeconds = $EntityExpirySeconds;        
    }
    
    private function HashCallSite(array $TraceInfo)
    {
        return md5(json_encode($TraceInfo));
    }
    
    
    public function Load(Object\IRequest $Request) {
        $Entities = parent::Load($Request);
        
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
        
        $Entity = parent::LoadByIdentitiy($Identity);
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
    
    public function Discard(&$Entity) {
        $this->RemoveFromCache($Entity);
        
        parent::Discard($Entity);
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
        $this->Cache->Save($IdentityHash, $Entity, $this->EntityExpirySeconds);
    }

    // </editor-fold>
}

?>