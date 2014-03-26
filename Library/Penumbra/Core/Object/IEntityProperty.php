<?php

namespace Penumbra\Core\Object;

/**
 * The entity property represents a property that contains one or none related entities.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IEntityProperty extends IRelationshipProperty {
    const IEntityPropertyType = __CLASS__;
    
    /**
     * Whether or not the parent entity is identified by the relationship
     * represented by this property
     * 
     * @return boolean
     */
    public function IsIdentity();
    
    /**
     * @return boolean
     */
    public function IsOptional();
    
    /**
     * This method should be implemented such that it returns the nessecary
     * relationship change and syncs the related entity.
     * 
     * @param UnitOfWork $UnitOfWork The unit of work to persist to
     * @param object $Entity The parent entity
     * @return RelationshipChange
     */
    public function Persist(UnitOfWork $UnitOfWork, $ParentEntity);
    
    /**
     * This method should be implemented such that it returns the nessecary
     * relationship change and discards the related entity.
     * 
     * @param UnitOfWork $UnitOfWork The unit of work to discard from
     * @param object $Entity The parent entity
     * @return RelationshipChange
     */
    public function Discard(UnitOfWork $UnitOfWork, $ParentEntity);
}

?>