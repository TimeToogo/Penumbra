<?php

namespace Storm\Core\Object;

interface IEntityProperty extends IRelationshipProperty {
    const IEntityPropertyType = __CLASS__;
    
    /**
     * @return RelationshipChange
     */
    public function Persist(UnitOfWork $UnitOfWork, $ParentEntity);
    /**
     * @return RelationshipChange
     */
    public function Discard(UnitOfWork $UnitOfWork, $ParentEntity);
}

?>