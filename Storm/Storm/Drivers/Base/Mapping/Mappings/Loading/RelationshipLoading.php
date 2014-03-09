<?php

namespace Storm\Drivers\Base\Mapping\Mappings\Loading;

use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

abstract class RelationshipLoading {
    
    public function AddToRelationalRequest(
            Mapping\IEntityRelationalMap $EntityRelationalMap, 
            Relational\IToOneRelation $ToOneRelation, 
            Relational\ResultSetSelect $Select) {
        $Select->AddColumns($ToOneRelation->GetRelationalParentColumns());
    }    
    
    final protected function LoadRelatedRows(
            Relational\IRelation $Relation,
            Relational\Database $Database, 
            array $ParentRows, 
            Object\RevivalData $AlreadyKnownRevivalData = null) {
        $RelatedRowRequest = $Relation->RelationSelect(Relational\SelectType::ResultSet, $ParentRows);
        $this->MapEntityToRelationalRequest($RelatedRowRequest, $AlreadyKnownRevivalData);
        return $Database->Load($RelatedRowRequest);
    }
    
    final protected function MapEntityToRelationalRequest(
            Mapping\IEntityRelationalMap $EntityRelationalMap,
            Relational\ResultSetSelect $Select, 
            Object\RevivalData $AlreadyKnownRevivalData = null) {
        if($AlreadyKnownRevivalData !== null) {
            $AlreadyKnownPropertyIdentifiers = array_keys($AlreadyKnownRevivalData->GetPropertyData());
            $AlreadyKnownProperties = $AlreadyKnownRevivalData->GetProperties($AlreadyKnownPropertyIdentifiers);
            $EntityRelationalMap->MapEntityToSelect($Select, $AlreadyKnownProperties);
        }
        else {
            $EntityRelationalMap->MapEntityToSelect($Select);
        }
    }
}

?>