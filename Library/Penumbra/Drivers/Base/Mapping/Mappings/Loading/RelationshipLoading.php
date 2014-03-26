<?php

namespace Penumbra\Drivers\Base\Mapping\Mappings\Loading;

use \Penumbra\Core\Mapping;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;

abstract class RelationshipLoading {
    
    final protected function LoadRelatedRows(
            Mapping\IEntityRelationalMap $EntityRelationalMap,
            Relational\IRelation $Relation,
            Relational\Database $Database, 
            array $ParentRows, 
            Object\RevivalData $AlreadyKnownRevivalData = null) {
        $RelatedRowSelect = $Relation->RelationResultSetSelect($ParentRows);
        $this->MapEntityToSelect($EntityRelationalMap, $RelatedRowSelect, $AlreadyKnownRevivalData);
        return $Database->Load($RelatedRowSelect);
    }
    
    final protected function MapEntityToSelect(
            Mapping\IEntityRelationalMap $EntityRelationalMap,
            Relational\ResultSetSelect $Select, 
            Object\RevivalData $AlreadyKnownRevivalData = null) {
        if($AlreadyKnownRevivalData !== null) {
            $AlreadyKnownPropertyIdentifiers = array_keys($AlreadyKnownRevivalData->GetData());
            $AlreadyKnownProperties = $AlreadyKnownRevivalData->GetProperties($AlreadyKnownPropertyIdentifiers);
            $EntityRelationalMap->MapPropertiesToSelect($Select, $AlreadyKnownProperties);
        }
        else {
            $EntityRelationalMap->MapPropertiesToSelect($Select);
        }
    }
}

?>