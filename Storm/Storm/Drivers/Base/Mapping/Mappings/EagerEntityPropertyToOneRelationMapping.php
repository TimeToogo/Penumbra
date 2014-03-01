<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping\DomainDatabaseMap;
use \Storm\Core\Object;
use \Storm\Core\Relational;

/**
 * Eager entity one mapping is optimized to join the rows together rather than
 * two individual queries.
 */
class EagerEntityPropertyToOneRelationMapping extends EntityPropertyToOneRelationMapping {
    public function __construct(Object\IEntityProperty $EntityProperty, Relational\IToOneRelation $ToOneRelation) {
        parent::__construct($EntityProperty, $ToOneRelation);
    }
    
    
    public function AddToRelationalRequest(Relational\Request $RelationalRequest) {
        $this->MapEntityToRelationalRequest($RelationalRequest);

        $this->GetToOneRelation()->AddRelationToRequest($RelationalRequest);
    }
    
    public function Revive(Relational\Database $Database, array $ResultRowArray, array $RevivalDataArray) {
        $ParentKeyRelatedRevivalDataMap = array_fill_keys(array_keys($ResultRowArray), null);
        $this->UnsetNullRows($RevivalDataArray);
        $ParentKeyRelatedRevivalDataMap += $this->EntityRelationalMap->MapResultRowsToRevivalData($Database, $ResultRowArray);
        
        foreach($RevivalDataArray as $Key => $RevivalData) {            
            $RevivalData[$this->Property] = $ParentKeyRelatedRevivalDataMap[$Key];
        }
    }
    
    private function UnsetNullRows(array &$ResultRows) {
        $ReviveColumns = $this->EntityRelationalMap->GetAllMappedReviveColumns();
        foreach($ResultRows as $Key => $ResultRow) {
            $Data = array_intersect_key($ResultRow->GetData(), $ReviveColumns);
            if(array_filter($Data, 'is_null') === count($Data)) {
                unset($ResultRows[$Key]);
            }
        }
    }
}

?>