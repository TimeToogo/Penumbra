<?php

namespace Penumbra\Core\Object;

/**
 * The collection property represents a property that contains a variable
 * amount of related entities.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface ICollectionProperty extends IRelationshipProperty {
    const ICollectionPropertyType = __CLASS__;
    
    /**
     * This method should be implemented such that it returns the nessecary
     * relationship changes and syncs the related entities.
     * 
     * @param UnitOfWork $UnitOfWork The unit of work to persist to
     * @param object $Entity The parent entity
     * @return RelationshipChange[]
     */
    public function Persist(UnitOfWork $UnitOfWork, $Entity);
    
    /**
     * This method should be implemented such that it returns the nessecary
     * relationship changes and discards all the related entities.
     * 
     * @param UnitOfWork $UnitOfWork The unit of work to persist to
     * @param object $Entity The parent entity
     * @return RelationshipChange[]
     */
    public function Discard(UnitOfWork $UnitOfWork, $Entity);
}

?>