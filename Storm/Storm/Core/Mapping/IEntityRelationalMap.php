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
    
    public function Initialize(DomainDatabaseMap $DomainDatabaseMap);
    
    public function Revive(DomainDatabaseMap $DomainDatabaseMap, Map $ResultRowRevivalDataMap);
    public function Persist(Relational\Transaction $Transaction, array $PersistenceData);
    public function Discard(Relational\Transaction $Transaction, array $Identities);
    
    /**
     * @return IPropertyMapping[]
     */
    public function GetPropertyMappings();
    
    /**
     * @return IDataPropertyColumnMapping[]
     */
    public function GetDataPropertyColumnMappings();
    
    /**
     * @return IEntityPropertyToOneRelationMapping[]
     */
    public function GetEntityPropertyToOneRelationMappings();
    
    /**
     * @return ICollectionPropertyToManyRelationMapping[]
     */
    public function GetCollectionPropertyToManyRelationMappings();
    
    
    /**
     * @return Relational\ResultRow
     */
    public function ResultRow($ColumnData = array());
    
    /**
     * @return Relational\Request[]
     */
    public function RelationalRequest(Object\IRequest $ObjectRequest);
    
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
    
    /**
     * @return Relational\PrimaryKey
     */
    public function MapIdentityToPrimaryKey(Object\Identity $Identity);
    /**
     * @return Object\Identity
     */
    public function MapPrimaryKeyToIdentity(Relational\PrimaryKey $PrimaryKey);
}

?>