<?php

namespace Storm\Core\Object;

interface ICollectionProperty extends IRelationshipProperty {
    const ICollectionPropertyType = __CLASS__;
    
    /**
     * @return RelationshipChange[]
     */
    public function Persist(UnitOfWork $UnitOfWork, $Entity);
    /**
     * @return RelationshipChange[]
     */
    public function Discard(UnitOfWork $UnitOfWork, $Entity);
}

?>