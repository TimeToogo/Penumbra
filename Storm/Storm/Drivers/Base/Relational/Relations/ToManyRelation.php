<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

class ToManyRelation extends ToManyRelationBase {    
    protected function FillParentToRelatedRowsMap(Map $Map, ForeignKey $ForeignKey, array $ParentRows, array $RelatedRows) {
        $ReferencedColumns = $ForeignKey->GetReferencedColumns();
        $ParentColumns = $ForeignKey->GetParentColumns();
        
        $GroupedRelatedRows = $this->GroupRelatedRows($RelatedRows, $ReferencedColumns);
        $this->MapParentRowsToGroupedRelatedRows($Map, $ParentRows, $ParentColumns, $GroupedRelatedRows);
    }

    protected function MapParentRowToRelatedKey(ForeignKey $ForeignKey, Relational\ResultRow $ParentRow) {
        $ParentKey = $ParentRow->GetDataFromColumns($ForeignKey->GetParentColumns());
        $ReferencedKey = new Relational\ResultRow($ForeignKey->GetReferencedColumns());
        $ForeignKey->MapParentToReferencedKey($ParentKey, $ReferencedKey);
        
        return $ReferencedKey;
    }
}

?>