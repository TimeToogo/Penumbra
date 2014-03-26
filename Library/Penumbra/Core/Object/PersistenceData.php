<?php

namespace Penumbra\Core\Object;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class PersistenceData extends EntityPropertyData {
    /**
     * @var Identity
     */
    private $Identity;
    
    private $Entity;
    
    public function __construct(IEntityMap $EntityMap, $Entity, array $EntityData = []) {
        parent::__construct($EntityMap, $EntityMap->GetProperties(), $EntityData);
        
        $this->Identity = new Identity($EntityMap, $EntityData);
        $this->Entity = $Entity;
    }
    
    /**
     * @return Identity
     */
    final public function GetIdentity() {
        return $this->Identity;
    }
    
    /**
     * @return Identity
     */
    final public function AnotherPersistenceData($Entity, array $PersistanceData = []) {
        $Another = $this->Another($PersistanceData);
        $Another->Entity = $Entity;
        $Another->Identity = clone $this->Identity;
        
        return $Another;
    }
    
    public function ReviveIdentity() {
        $this->Identity->Apply($this->Entity);
    }
    
    public function GetData() {
        return $this->Identity->Data + $this->Data;
    }
    
    public function SetData(array $Data) {
        $this->Identity->SetData($Data);
        parent::SetData($Data);
    }
    
    protected function GetPropertyData(IProperty $Property) {
        if($this->EntityMap->HasIdentityProperty($Property)) {
            return $this->Identity->GetPropertyData($Property);
        }
        else {
            return parent::GetPropertyData($Property);
        }
    }
    
    protected function SetPropertyData(IProperty $Property, $Data) {
        if($this->EntityMap->HasIdentityProperty($Property)) {
            $this->Identity->SetPropertyData($Property, $Data);
        }
        else {
            parent::SetPropertyData($Property, $Data);
        }
    }
    
    protected function HasPropertyData(IProperty $Property) {
        if($this->EntityMap->HasIdentityProperty($Property)) {
            return $this->Identity->HasPropertyData($Property);
        }
        else {
            return parent::HasPropertyData($Property);
        }
    }
    
    protected function RemovePropertyData(IProperty $Property) {
        if($this->EntityMap->HasIdentityProperty($Property)) {
            $this->Identity->RemovePropertyData($Property);
        }
        else {
            parent::RemovePropertyData($Property);
        }
    }
}

?>