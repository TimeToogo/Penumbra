<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Object;
use \Storm\Core\Relational;

interface IDataPropertyColumnMapping extends IPropertyMapping {
    const IDataPropertyColumnMappingType = __CLASS__;
    
    public function IsIdentityPrimaryKeyMapping();
    
    /**
     * @return Object\IDataProperty
     */
    public function GetDataProperty();
    
    /**
     * @return Relational\IColumn[]
     */
    public function GetReviveColumns();
    
    /**
     * @return Relational\IColumn[]
     */
    public function GetPersistColumns();    
    
    public function Revive(Map $ColumnDataPropertyDataMap);
    public function Persist($DataPropertyValue, Relational\ColumnData $ColumnData);
}

?>