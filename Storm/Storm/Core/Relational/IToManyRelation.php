<?php

namespace Storm\Core\Relational;

interface IToManyRelation extends IRelation {
    const IToManyRelationType = __CLASS__;
    
    public function Persist(Transaction $Transaction, ColumnData $ParentData, array $PersistedRelatedRows, array $DiscardedPrimaryKeys);
    public function Discard(Transaction $Transaction, PrimaryKey $PrimaryKey);
}

?>