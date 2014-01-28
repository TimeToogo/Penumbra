<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class EagerEntityPropertyToOneRelationMapping extends EntityPropertyToOneRelationMapping {
    
    public function AddToRelationalRequest(DomainDatabaseMap $DomainDatabaseMap, Relational\Request $RelationalRequest) {
        if(!$this->GetEntityProperty()->IsOptional()) {
            $DomainDatabaseMap->MapEntityToRelationalRequest(
                    $this->GetEntityType(),
                    $RelationalRequest);

            $this->GetToOneRelation()->AddRelationToRequest($RelationalRequest);
        }
    }
    
    public function Revive(DomainDatabaseMap $DomainDatabaseMap, array $ResultRowArray, array &$RevivalDataArray) {
        $PropertyIdentifier = $this->GetProperty()->GetIdentifier();
        
        if(!$this->GetEntityProperty()->IsOptional()) {
            $JoinedRows = $ResultRowArray;
            $RelatedRevivalDataArray = $DomainDatabaseMap->MapRowsToRevivalData($this->GetEntityType(), $JoinedRows);
            
            foreach($RevivalDataArray as $Key => $RevivalData) {
                $RevivalData[$PropertyIdentifier] = $RelatedRevivalDataArray[$Key];
            }
        }
        else {
            $RelatedRows = $this->LoadRelatedRows($DomainDatabaseMap, $ResultRowArray);
            
            $ParentKeyRelatedRowMap = $this->ToOneRelation->MapParentKeysToRelatedRow($ResultRowArray, $RelatedRows);
            
            $RelatedRevivalDataArray = $DomainDatabaseMap->MapRowsToRevivalData($this->GetEntityType(), $ParentKeyRelatedRowMap);

            foreach($RevivalDataArray as $Key => &$RevivalData) {
                if(isset($RelatedRevivalDataArray[$Key])) {
                    $RevivalData[$PropertyIdentifier] = $RelatedRevivalDataArray[$Key];
                }
                else {
                    $RevivalData[$PropertyIdentifier] = null;
                }
            }
        }
    }
}

?>