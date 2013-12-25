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
     * @return Relational\Table
     */
    public function GetPrimaryKeyTable();
    
    /**
     * @return IPropertyMapping[]
     */
    public function GetPropertyMappings();
    
    /**
     * @return IIdentityPrimaryKeyMapping[]
     */
    public function GetIdentityPrimaryKeyMappings();
    
    /**
     * @return IPropertyColumnMapping[]
     */
    public function GetPropertyColumnMappings();
    
    /**
     * @return IPropertyRelationMapping[]
     */
    public function GetProperyRelationMappings();
    
    /**
     * @return Relational\IColumn[]
     */
    public function GetMappedColumns(Object\IProperty $Property);
    
    /**
     * @return Object\IProperty[]
     */
    public function GetMappedProperties(Relational\IColumn $Column);
    
    /**
     * @return Relational\IColumn[]
     */
    public function GetAllMappedColumns(array $Properties = null);
    
    /**
     * @return Object\IProperty[]
     */
    public function GetAllMappedProperties(array $Columns = null);
    
    public function MapPropertyDataToColumnData(Object\PropertyData $PropertyData, Relational\ColumnData $ColumnData);
    public function MapColumnDataToPropertyData(Relational\ColumnData $ColumnData, Object\PropertyData $PropertyData);
}

?>