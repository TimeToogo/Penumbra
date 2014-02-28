<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Object;
use \Storm\Core\Relational;

/**
 * The interface representing data property mapped to columns
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IDataPropertyColumnMapping extends IPropertyMapping {
    const IDataPropertyColumnMappingType = __CLASS__;
    
    /**
     * Whether or not this is mapping an identity/primary key
     * 
     * @return boolean
     */
    public function IsIdentityPrimaryKeyMapping();
    
    /**
     * The mapped data property
     * 
     * @return Object\IDataProperty
     */
    public function GetDataProperty();
    
    /**
     * The columns to load when reviving.
     * 
     * @return Relational\IColumn[]
     */
    public function GetReviveColumns();
    
    /**
     * The columns in which data is persisted.
     * 
     * @return Relational\IColumn[]
     */
    public function GetPersistColumns();
    
    /**
     * Maps the column data to property data.
     * 
     * @param Relational\ColumnData[] $ColumnDataArray The array containing the column data
     * @param Object\PropertyData[] $ColumnDataArray The array containing the property data
     * @return void
     */
    public function Revive(array $ColumnDataArray, array $PropertyDataArray);
    
    /**
     * Maps the property value to the supplied column data.
     * 
     * @param Object\PropertyData[] $PropertyDataArray The array containing the property data
     * @param Relational\ColumnData[] $ColumnDataArray The array containing the column data
     * @return void
     */
    public function Persist(array $PropertyDataArray, array $ColumnDataArray);
}

?>