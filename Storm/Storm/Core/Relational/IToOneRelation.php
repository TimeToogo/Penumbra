<?php

namespace Storm\Core\Relational;

interface IToOneRelation extends IRelation {
    const IToOneRelationType = __CLASS__;
    
    public function Persist(Transaction $Transaction, ColumnData $ParentData, Row $RelatedRow);
    public function Discard(Transaction $Transaction, PrimaryKey $PrimaryKey);
}

?>