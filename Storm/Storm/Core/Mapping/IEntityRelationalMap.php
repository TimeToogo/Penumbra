<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Object;
use \Storm\Core\Relational;

interface IEntityRelationalMap {
    const IEntityRelationalMapType = __CLASS__;
    
    public function GetEntityType();
    
    /**
     * @return Object\EntityMap
     */
    public function GetEntityMap();
    
    /**
     * @return IPropertyMapping[]
     */
    public function GetPropertyMappings();
    
    /**
     * @return IPropertyColumnMapping[]
     */
    public function GetPropertyColumnMappings();
    
    /**
     * @return Relational\IColumn[]
     */
    public function GetMappedColumn(Object\IProperty $Property);
    
    /**
     * @return Object\IProperty[]
     */
    public function GetMappedProperty(Relational\IColumn $Column);
    
    /**
     * @return IPropertyRelationMapping[]
     */
    public function GetProperyRelationMappings();
    
    public function AddToRelationalRequest(Relational\Request $Request);
}

?>