<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class EagerEntityPropertyToOneRelationMapping extends EntityPropertyToOneRelationMapping {
    public function __construct(Object\IEntityProperty $EntityProperty, Relational\IToOneRelation $ToOneRelation) {
        parent::__construct($EntityProperty, $ToOneRelation);
    }
    
    
    public function AddToRelationalRequest(Relational\Request $RelationalRequest) {
        $this->MapEntityToRelationalRequest($RelationalRequest);

        $this->GetToOneRelation()->AddRelationToRequest($RelationalRequest);
    }
    
    public function Revive(Relational\Database $Database, array $ResultRowArray, array $RevivalDataArray) {
        $ParentKeyRelatedRevivalDataMap = $this->EntityRelationalMap->MapResultRowsToRevivalData($Database, $ResultRowArray);
        
        foreach($RevivalDataArray as $Key => $RevivalData) {            
            $RevivalData[$this->Property] = $ParentKeyRelatedRevivalDataMap[$Key];
        }
    }
}

?>