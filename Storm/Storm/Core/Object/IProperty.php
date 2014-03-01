<?php

namespace Storm\Core\Object;

/**
 * The base for a type representing a property of an entity.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IProperty {
    const IPropertyType = __CLASS__;
    
    /**
     * The value identifier for the property.
     * 
     * @return string
     */
    public function GetIdentifier();
    
    /**
     * The expression tree for the property getter.
     * 
     * @return Expressions\Expression
     */
    public function GetGetterExpression();
    
    /**
     * The expression tree for the property setter (without setting value).
     * 
     * @return Expressions\Expression
     */
    public function GetSetterExpression();
    
    /**
     * The parent entity type.
     * 
     * @return IEntityMap|null
     */
    public function GetEntityType();
    
    /**
     * Sets the parent entity type.
     * 
     * @param string $EntityType
     * @return void
     */
    public function SetEntityType($EntityType);
}

?>