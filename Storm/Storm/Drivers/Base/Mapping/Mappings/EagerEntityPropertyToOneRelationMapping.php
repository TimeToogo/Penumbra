<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class EagerEntityPropertyToOneRelationMapping extends EntityPropertyToOneRelationMapping {
    private $WillJoinRows;
    
    public function __construct(Object\IEntityProperty $EntityProperty, Relational\IToOneRelation $ToOneRelation) {
        parent::__construct($EntityProperty, $ToOneRelation);
        
        $this->WillJoinRows = !$this->GetEntityProperty()->IsOptional();
    }
    
    
    public function AddToRelationalRequest(DomainDatabaseMap $DomainDatabaseMap, Relational\Request $RelationalRequest) {
        if($this->WillJoinRows) {
            $DomainDatabaseMap->MapEntityToRelationalRequest(
                    $this->GetEntityType(),
                    $RelationalRequest);

            $this->GetToOneRelation()->AddRelationToRequest($RelationalRequest);
        }
    }
    
    public function Revive(DomainDatabaseMap $DomainDatabaseMap, array $ResultRowArray, array $RevivalDataArray) {
        $ParentKeyRelatedRevivalDataMap = null;
        
        if(!$this->WillJoinRows) {
            $ParentKeyRelatedRevivalDataMap = $DomainDatabaseMap->MapRowsToRevivalData($this->GetEntityType(), $ResultRowArray);
        }
        else {
            $RelatedRowArray = $this->LoadRelatedRows($DomainDatabaseMap, $ResultRowArray);
            
            $ParentKeyRelatedRevivalDataMap = $this->MapParentRowKeysToRelatedRevivalData($DomainDatabaseMap, $ResultRowArray, $RelatedRowArray);
        }
        
        foreach($RevivalDataArray as $Key => $RevivalData) {            
            $RevivalData[$this->Property] = $ParentKeyRelatedRevivalDataMap[$Key];
        }
    }
}

?>