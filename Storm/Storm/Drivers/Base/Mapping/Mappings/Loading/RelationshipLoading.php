<?php

namespace Storm\Drivers\Base\Mapping\Mappings\Loading;

use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

abstract class RelationshipLoading {
    
    public function AddToRelationalRequest(
            Mapping\IEntityRelationalMap $EntityRelationalMap, 
            Relational\IToOneRelation $ToOneRelation, 
            Relational\Request $RelationalRequest) {
        $RelationalRequest->AddColumns($ToOneRelation->GetRelationalParentColumns());
    }    
    
    final protected function LoadRelatedRows(
            Relational\IRelation $Relation,
            Relational\Database $Database, 
            array $ParentRows, 
            Object\RevivalData $AlreadyKnownRevivalData = null) {
        $RelatedRowRequest = $Relation->RelationRequest($ParentRows);
        $this->MapEntityToRelationalRequest($RelatedRowRequest, $AlreadyKnownRevivalData);
        return $Database->Load($RelatedRowRequest);
    }
    
    final protected function MapEntityToRelationalRequest(
            Mapping\IEntityRelationalMap $EntityRelationalMap,
            Relational\Request $RelationalRequest, 
            Object\RevivalData $AlreadyKnownRevivalData = null) {
        if($AlreadyKnownRevivalData !== null) {
            $AlreadyKnownPropertyIdentifiers = array_keys($AlreadyKnownRevivalData->GetPropertyData());
            $AlreadyKnownProperties = $AlreadyKnownRevivalData->GetProperties($AlreadyKnownPropertyIdentifiers);
            $EntityRelationalMap->MapEntityToRelationalRequest($RelationalRequest, $AlreadyKnownProperties);
        }
        else {
            $EntityRelationalMap->MapEntityToRelationalRequest($RelationalRequest);
        }
    }
}

?>