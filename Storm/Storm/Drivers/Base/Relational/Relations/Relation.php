<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;

abstract class Relation implements Relational\IRelation {
    use \Storm\Core\Helpers\Type;
    
    private $Table;
    private $PersistingOrder;
    private $DiscardingOrder;
    
    public function __construct(Relational\Table $Table, $PersistingOrder, $DiscardingOrder) {
        $this->Table = $Table;
        $this->PersistingOrder = $PersistingOrder;
        $this->DiscardingOrder = $DiscardingOrder;
    }
    
    /**
     * @return Relational\Table
     */
    final public function GetTable() {
        return $this->Table;
    }
    
    final public function GetPersistingDependencyOrder() {
        return $this->PersistingOrder;
    }
    
    final public function GetDiscardingDependencyOrder() {
        return $this->DiscardingOrder;
    }
    
    final public function MapRelatedRows(array $ParentRows, array $RelatedRows) {
        $Map = new Map();
        $this->MapParentToRelatedRows($Map, $ParentRows, $RelatedRows);
        
        return $Map;
    }
    protected abstract function MapParentToRelatedRows(Map $Map, array $ParentRows, array $RelatedRows);
}

?>