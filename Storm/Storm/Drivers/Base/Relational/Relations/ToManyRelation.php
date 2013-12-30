<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

class ToManyRelation extends ToManyRelationBase {
    public function __construct(ForeignKey $ForeignKey, Relational\Table $RelatedTable) {
        parent::__construct($ForeignKey, $RelatedTable,
                Relational\DependencyOrder::Before, Relational\DependencyOrder::Before);
    }
    protected function FillParentToRelatedRowsMap(Map $Map, ForeignKey $ForeignKey, array $ParentRows, array $RelatedRows) {
        $ReferencedColumns = $ForeignKey->GetReferencedColumns();
        $ParentColumns = $ForeignKey->GetParentColumns();
        
        $GroupedRelatedRows = $this->GroupRelatedRows($RelatedRows, $ParentColumns);
        $this->MapParentRowsToGroupedRelatedRows($Map, $ParentRows, $ReferencedColumns, $GroupedRelatedRows);
    }

    protected function MapParentRowToRelatedKey(ForeignKey $ForeignKey, Relational\ResultRow $ParentRow) {
        $ParentKey = $ParentRow->GetDataFromColumns($ForeignKey->GetReferencedColumns());
        $ReferencedKey = new Relational\ResultRow($ForeignKey->GetParentColumns());
        $ForeignKey->MapReferencedToParentKey($ParentKey, $ReferencedKey);
        
        return $ReferencedKey;
    }
}

?>