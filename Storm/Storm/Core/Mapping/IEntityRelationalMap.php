<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Object;
use \Storm\Core\Relational;

/**
 * The IEntityRelationalMap interface holds all the property mappings for the entity type.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IEntityRelationalMap {
    const IEntityRelationalMapType = __CLASS__;
    
    /**
     * The type of entity that this relational map is representing.
     * 
     * @return string
     */
    public function GetEntityType();
    
    /**
     * The entity map of represented entity.
     * 
     * @return Object\IEntityMap
     */
    public function GetEntityMap();
    
    /**
     * The table in which the identity is stored.
     * 
     * @return Relational\ITable
     */
    public function GetPrimaryKeyTable();
    
    
    /**
     * The properties that mapped.
     * 
     * @return Object\IProperties[]
     */
    public function GetMappedProperties();
    
    /**
     * The tables that are mapped to for persistence.
     * 
     * @return Relational\ITable[]
     */
    public function GetMappedPersistTables();
    
    /**
     * The tables that are mapped from for revival.
     * 
     * @return Relational\ITable[]
     */
    public function GetMappedReviveTables();
    
    /**
     * Intialize the property mapping in the context of the parent domain database map.
     * 
     * @param DomainDatabaseMap $DomainDatabaseMap The parent domain database map
     * @return void
     */
    public function Initialize(DomainDatabaseMap $DomainDatabaseMap);
    
    /**
     * @return IPropertyMapping[]
     */
    public function GetPropertyMappings();
    
    /**
     * @return IDataPropertyColumnMapping[]
     */
    public function GetDataPropertyColumnMappings();
    
    /**
     * @return IDataPropertyColumnMapping[]
     */
    public function GetIdentityPropertyPrimaryKeyMappings();
    
    /**
     * @return IEntityPropertyToOneRelationMapping[]
     */
    public function GetEntityPropertyToOneRelationMappings();
    
    /**
     * @return ICollectionPropertyToManyRelationMapping[]
     */
    public function GetCollectionPropertyToManyRelationMappings();
    
    
    /**
     * Instantiate a new result row for the given column data.
     * 
     * @param array $ColumnData the column data
     * @return Relational\ResultRow
     */
    public function ResultRow($ColumnData = array());
    
    /**
     * Get the criterion that must be included for every request.
     * 
     * @return Relational\Criterion
     */
    public function GetCriterion();
    
    /**
     * @param Object\IProperty $Property The mapped property
     * @return Relational\IColumn[]
     */
    public function GetMappedReviveColumns(Object\IProperty $Property);
    
    /**
     * @param Object\IProperty $Property The mapped property
     * @return Relational\IColumn[]
     */
    public function GetMappedPersistColumns(Object\IProperty $Property);
    
    /**
     * @param Object\IProperty[]|null $Properties The mapped properties
     * @return Relational\IColumn[]
     */
    public function GetAllMappedReviveColumns(array $Properties = null);
    
    /**
     * @param Object\IProperty[]|null $Properties The mapped properties
     * @return Relational\IColumn[]
     */
    public function GetAllMappedPersistColumns(array $Properties = null);
    
    /**
     * Maps an identity to the equivalent primary key.
     * 
     * @param Object\Identity $Identity The identity to map
     * @return Relational\PrimaryKey The mapped primary key
     */
    public function MapIdentityToPrimaryKey(Object\Identity $Identity);
    
    /**
     * Maps an array of identities to the equivalent primary keys.
     * NOTE: Keys are preserved
     * 
     * @param Object\Identity[] $Identity The identities to map
     * @return Relational\PrimaryKey[] The mapped primary keys
     */
    public function MapIdentitiesToPrimaryKeys(array $Identities);
    
    /**
     * Map a primary key to the equivalent identity.
     * 
     * @param Relational\PrimaryKey The map primary key to map
     * @return Object\Identity $Identity The mapped identity
     */
    public function MapPrimaryKeyToIdentity(Relational\PrimaryKey $PrimaryKey);
    
    /**
     * Maps an array of primary keysto the equivalent identities.
     * NOTE: Keys are preserved
     * 
     * @param Relational\PrimaryKey[] The primary keys to map
     * @return Object\Identity[] $Identity The mapped identities
     */
    public function MapPrimaryKeysToIdentities(array $PrimaryKeys);
}

?>