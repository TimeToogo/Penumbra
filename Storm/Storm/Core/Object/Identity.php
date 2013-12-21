<?php

namespace Storm\Core\Object;

final class Identity extends PropertyData {
    public function __construct(EntityMap $EntityMap, array $IdentityData = array()) {
        parent::__construct($EntityMap, $IdentityData);
    }
    
    protected function AddProperty($PropertyName, $Data) {
        if(!$this->GetEntityMap()->HasIdentityProperty($PropertyName))
            throw new \InvalidArgumentException('$PropertyName must be a valid Identity property of ' . get_class($this->GetEntityMap()));
        
        parent::AddProperty($PropertyName, $Data);
    }
    
    public function Is(self $OtherIdentity) {
        return $this->GetEntityType() === $OtherIdentity->GetEntityType()
                && $this->GetPropertyData() === $OtherIdentity->GetPropertyData();
    }
    
    public function Hash() {
        $PropertyData = $this->GetPropertyData();
        $AllData = array_merge([$this->GetEntityType()], array_keys($PropertyData), array_values($PropertyData));
        
        return md5(implode(' ', $AllData));
    }
}

?>