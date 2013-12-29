<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Mapping\IEntityPropertyToOneRelationMapping;
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

    public function Revive(DomainDatabaseMap $DomainDatabaseMap, Map $ResultRowRevivalDataMap) {
        if(!$this->GetEntityProperty()->IsOptional()) {
            $JoinedRows = $ResultRowRevivalDataMap->GetInstances();
            $RelatedRevivalData = $DomainDatabaseMap->MapRowsToRevivalData($this->GetEntityType(), $JoinedRows);
            $Property = $this->GetProperty();
            foreach($JoinedRows as $Key => $JoinedRow) {
                $ParentRevivalData = $ResultRowRevivalDataMap[$JoinedRow];
                $ParentRevivalData[$Property] = $RelatedRevivalData[$Key];
            }
        }
        else {
            $ParentRows = $ResultRowRevivalDataMap->GetInstances();            
            $RelatedRows = $this->LoadRelatedRows($DomainDatabaseMap, $ParentRows);
            
            $ParentRowRelatedRevivalDataMap = $this->MapToParentRowRelatedRevivalDataMap($DomainDatabaseMap, $ResultRowRevivalDataMap, $RelatedRows);
            
            $Property = $this->GetProperty();
            foreach($ParentRowRelatedRevivalDataMap as $ParentRow) {
                $RelatedRevivalData = $ParentRowRelatedRevivalDataMap[$ParentRow];
                $ParentRevivalData = $ResultRowRevivalDataMap[$ParentRow];
                
                $ParentRevivalData[$Property] = $RelatedRevivalData;
            }
        }
    }
}

?>