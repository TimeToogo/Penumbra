<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

class ToOneRelation extends ToOneRelationBase {    
    protected function FillParentToRelatedRowMap(Map $Map, ForeignKey $ForeignKey, array $ParentRows, array $RelatedRows) {
        $KeyedParentRows = $this->HashRowsByColumns($ParentRows, $ForeignKey->GetParentColumns());
        $KeyedRelatedRows = $this->HashRowsByColumns($RelatedRows, $ForeignKey->GetReferencedColumns());
        $this->MapKeyIntersection($Map, $KeyedParentRows, $KeyedRelatedRows);
    }

    protected function MapParentRowToRelatedKey(ForeignKey $ForeignKey, Relational\ResultRow $ParentRow) {
        $ParentKey = $ParentRow->GetDataFromColumns($ForeignKey->GetParentColumns());
        $ReferencedKey = new Relational\ResultRow($ForeignKey->GetReferencedColumns());
        $ForeignKey->MapParentToReferencedKey($ParentKey, $ReferencedKey);
        
        return $ReferencedKey;
    }

}

?>