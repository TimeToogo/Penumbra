<?php

namespace Penumbra\Core\Object;

/**
 * This class represents the data of an entity which is to be revived.
 * This should contain the entity's identity, data values and relationship revival data.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class RevivalData extends EntityPropertyData {
    public function __construct(EntityMap $EntityMap, array $EntityData = []) {
        parent::__construct($EntityMap, $EntityMap->GetProperties(), $EntityData);
    }
    
    public function ReviveEntity() {
        $Entity = $this->EntityMap->ConstructEntity($this);
        $this->EntityMap->LoadEntity($this, $Entity);
        
        return $Entity;
    }
}

?>