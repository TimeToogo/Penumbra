<?php

namespace Storm\Core\Object;

/**
 * The data property represents a property which contains data subjective
 * to the parent entity.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IDataProperty extends IProperty {
    const IDataPropertyType = __CLASS__;
    
    /**
     * Whether or not the property is part of the entity's identity.
     * 
     * @return boolean
     */
    public function IsIdentity();
    
    /**
     * This method should be implemented such that it sets the value of the entity with 
     * the given property value
     * 
     * @param mixed $PropertyValue The property value to set
     * @param object $Entity The entity
     * @return void
     */
    public function ReviveValue($PropertyValue, $Entity);
    
    /**
     * This method should be implemented such that it ges the value of the entity entity property
     * 
     * @param object $Entity The entity to get the value from
     * @return mixed The property value
     */
    public function GetValue($Entity);
}

?>