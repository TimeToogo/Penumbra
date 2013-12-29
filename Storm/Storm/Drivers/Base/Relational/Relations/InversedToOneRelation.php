<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

class InversedToOneRelation extends ToOneRelationBase {    
    protected function FillParentToRelatedRowMap(Map $Map, ForeignKey $ForeignKey, array $ParentRows, array $RelatedRows) {
        $KeyedParentRows = $this->HashRowsByColumns($ParentRows, $ForeignKey->GetReferencedColumns());
        $KeyedRelatedRows = $this->HashRowsByColumns($RelatedRows, $ForeignKey->GetParentColumns());
        $this->MapKeyIntersection($Map, $KeyedParentRows, $KeyedRelatedRows);
    }

    protected function MapParentRowToRelatedKey(ForeignKey $ForeignKey, Relational\ResultRow $ParentRow) {
        $ReferencedKey = $ParentRow->GetDataFromColumns($ForeignKey->GetReferencedColumns());
        $ParentKey = new Relational\ResultRow($ForeignKey->GetParentColumns());
        $ForeignKey->MapReferencedToParentKey($ReferencedKey, $ParentKey);
        
        return $ParentKey;
    }

}

?>