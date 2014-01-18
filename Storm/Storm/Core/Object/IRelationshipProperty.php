<?php

namespace Storm\Core\Object;

/**
 * The base for a type representing a property of an entity containing a relationship
 * with other entities.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IRelationshipProperty extends IProperty {
    const IRelationshipPropertyType = __CLASS__;
    
    /**
     * The related entity type.
     * 
     * return string
     */
    public function GetEntityType();
    
    /**
     * return boolean
     */
    public function IsIdentifying();
    
    /**
     * This method should be implemented such that it sets the property
     * value of the supplied entity appropriately to the mapped revival value
     * 
     * @param Object\Domain $Domain
     * @param type $PropertyValue The mapped revival value
     * @param type $Entity The entity to set the value to
     */
    public function Revive(Domain $Domain, $PropertyValue, $Entity);
}

?>