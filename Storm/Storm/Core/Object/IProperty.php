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
     * This should return an unique identifier representing the property (human readable).
     * 
     * @return string
     */
    public function GetIdentifier();
        
    /**
     * Parses an entity traversal expression tree to the resolved property expression tree
     * 
     * @return Expression
     */
    public function ResolveTraversalExpression(Expressions\TraversalExpression $Expression, Expressions\PropertyExpression $ParentPropertyExpression = null);
    
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