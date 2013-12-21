<?php

namespace Storm\Core\Relational;

final class DependencyOrder {
    const Before = -1;
    const After = 1;
}

final class DependencyMode {
    const Persisting = 1;
    const Discarding = -1;
}

interface IRelation {    
    public function GetTable();
    public function GetPersistingDependencyOrder();
    public function GetDiscardingDependencyOrder();
    
    public function AddToRequest(Request $Request);
    /**
     * @return PrimaryKey
     */
    public function MapToRelatedPrimaryKey(ResultRow $ResultRow);
}

?>