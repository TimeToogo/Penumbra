<?php

namespace Storm\Core\Relational;

interface IToOneRelation extends IRelation {
    const IToOneRelationType = __CLASS__;
    
    /**
     * @return Map 
     */
    public function MapParentToRelatedRow(array $ParentRows, array $RelatedRows);
    
    public function Persist(Transaction $Transaction, ColumnData $ParentData, RelationshipChange $RelationshipChange);
}

?>