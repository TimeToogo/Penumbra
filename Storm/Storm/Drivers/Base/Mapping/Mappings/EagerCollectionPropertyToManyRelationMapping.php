<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

final class EagerCollectionPropertyToManyRelationMapping extends CollectionPropertyToManyRelationMapping {
    public function __construct(
            Object\ICollectionProperty $CollectionProperty, 
            Relational\IToManyRelation $ToManyRelation) {
        parent::__construct($CollectionProperty, $ToManyRelation);
    }
    
    public function Revive(Relational\Database $Database, array $ResultRowArray, array $RevivalDataArray) {
        $RelatedRowArray = $this->LoadRelatedRows($Database, $ResultRowArray);
        
        $ParentKeyRelatedRevivalDataArrayMap = $this->MapParentRowKeysToRelatedRevivalDataArray($Database, $ResultRowArray, $RelatedRowArray);
        
        foreach($RevivalDataArray as $Key => $RevivalData) {            
            $RevivalData[$this->Property] = $ParentKeyRelatedRevivalDataArrayMap[$Key];
        }
    }
}

?>