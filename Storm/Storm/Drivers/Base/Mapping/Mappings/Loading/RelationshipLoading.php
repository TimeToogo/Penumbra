<?php

namespace Storm\Drivers\Base\Mapping\Mappings\Loading;

use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

abstract class RelationshipLoading {
    
    public function AddLoadingRequirementsToSelect(
            Mapping\IEntityRelationalMap $EntityRelationalMap, 
            Relational\IToOneRelation $ToOneRelation, 
            Relational\ResultSetSelect $Select) {
        $Select->AddColumns($ToOneRelation->GetParentColumns());
    }    
    
    final protected function LoadRelatedRows(
            Relational\IRelation $Relation,
            Relational\Database $Database, 
            array $ParentRows, 
            Object\RevivalData $AlreadyKnownRevivalData = null) {
        $RelatedRowRequest = $Relation->RelationResultSetSelect($ParentRows);
        $this->MapEntityToSelect($RelatedRowRequest, $AlreadyKnownRevivalData);
        return $Database->Load($RelatedRowRequest);
    }
    
    final protected function MapEntityToSelect(
            Mapping\IEntityRelationalMap $EntityRelationalMap,
            Relational\ResultSetSelect $Select, 
            Object\RevivalData $AlreadyKnownRevivalData = null) {
        if($AlreadyKnownRevivalData !== null) {
            $AlreadyKnownPropertyIdentifiers = array_keys($AlreadyKnownRevivalData->GetPropertyData());
            $AlreadyKnownProperties = $AlreadyKnownRevivalData->GetProperties($AlreadyKnownPropertyIdentifiers);
            $EntityRelationalMap->MapPropetiesToSelect($Select, $AlreadyKnownProperties);
        }
        else {
            $EntityRelationalMap->MapPropetiesToSelect($Select);
        }
    }
}

?>