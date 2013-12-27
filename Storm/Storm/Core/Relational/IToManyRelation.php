<?php

namespace Storm\Core\Relational;

interface IToManyRelation extends IRelation {
    const IToManyRelationType = __CLASS__;
    
    /**
     * @return Map 
     */
    public function MapParentToRelatedRows(array $ParentRows, array $RelatedRows);
    
    public function Persist(Transaction $Transaction, ColumnData $ParentData, array $RelationshipChanges);
}

?>