<?php

namespace Storm\Core\Relational;

use \Storm\Core\Containers\Map;

interface IToOneRelation extends IRelation {
    const IToOneRelationType = __CLASS__;
    public function Persist(Transaction $Transaction, Row $Row, Row $RelatedRow);
    public function Discard(Transaction $Transaction, PrimaryKey $PrimaryKey);
}

?>