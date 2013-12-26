<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Containers\Map;
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
    
    public function Revive(RevivingContext $Context, Map $ResultRowRevivalDataMap);
    public function Persist(PersistingContext $Context, TransactionalContext $TransactionalContext);
    public function Discard(DiscardingContext $Context, TransactionalContext $TransactionalContext);
    
    /**
     * @return IPropertyMapping[]
     */
    public function GetPropertyMappings();
    
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
    public function GetMappedReviveColumns(Object\IProperty $Property);
    
    /**
     * @return Relational\IColumn[]
     */
    public function GetMappedPersistColumns(Object\IProperty $Property);
    
    /**
     * @return Relational\IColumn[]
     */
    public function GetAllMappedReviveColumns(array $Properties = null);
    
    /**
     * @return Relational\IColumn[]
     */
    public function GetAllMappedPersistColumns(array $Properties = null);
    
    public function MapPropertyDataToColumnData(Object\PropertyData $PropertyData, Relational\ColumnData $ColumnData);
    public function MapColumnDataToPropertyData(Relational\ColumnData $ColumnData, Object\PropertyData $PropertyData);
}

?>