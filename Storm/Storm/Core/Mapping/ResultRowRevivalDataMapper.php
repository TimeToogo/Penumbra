<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Core\Containers\Registrar;
use \Storm\Core\Containers\Map;

final class ResultRowRevivalDataMapper extends ObjectRelationalMapperBase {

    /**
     * @internal
     * @return Object\RevivalData[]
     */
    final public function MapRowsToRevivalData($EntityType, array $ResultRows) {
        $EntityRelationalMap = $this->DomainDatabaseMap->GetRelationMap($EntityType);

        $ResultRowRevivalDataMap = new Map();
        $RevivalDataArray = array();
        $EntityMap = $EntityRelationalMap->GetEntityMap();
        foreach ($ResultRows as $Key => $ResultRow) {
            $RevivalData = $EntityMap->RevivalData();
            $ResultRowRevivalDataMap[$ResultRow] = $RevivalData;
            $RevivalDataArray[$Key] = $RevivalData;
        }
        
        $this->MapResultRowsToRevivalData($EntityRelationalMap, $ResultRowRevivalDataMap);
        
        return $RevivalDataArray;
    }
    
    final public function MapResultRowsToRevivalData(IEntityRelationalMap $EntityRelationalMap, Map $ResultRowRevivalDataMap) {
        foreach($EntityRelationalMap->GetDataPropertyColumnMappings() as $PropertyColumnMapping) {
            $PropertyColumnMapping->Revive($ResultRowRevivalDataMap);
        }
        foreach($EntityRelationalMap->GetEntityPropertyToOneRelationMappings() as $EntityPropertyToOneRelationMapping) {
            $EntityPropertyToOneRelationMapping->Revive($this, $ResultRowRevivalDataMap);
        }
        foreach($EntityRelationalMap->GetCollectionPropertyToManyRelationMappings() as $CollectionPropertyToManyRelationMapping) {
            $CollectionPropertyToManyRelationMapping->Revive($this, $ResultRowRevivalDataMap);
        }
    }
}

?>