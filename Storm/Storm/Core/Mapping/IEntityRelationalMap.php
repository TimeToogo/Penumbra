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
     * The database for the entity relational map
     * 
     * @return Relational\Database
     */
    public function GetDatabase();
    
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
     * Intialize the entity map and tables in the context of the parent domain database map.
     * 
     * @param DomainDatabaseMap $DomainDatabaseMap The parent domain database map
     * @return void
     */
    public function Initialize(DomainDatabaseMap $DomainDatabaseMap);
    
    /**
     * Intialize the relationship property mapping in the context of the parent domain database map.
     * 
     * @param DomainDatabaseMap $DomainDatabaseMap The parent domain database map
     * @return void
     */
    public function InitializeRelationshipMappings(DomainDatabaseMap $DomainDatabaseMap);
    
    /**
     * @return IPropertyMapping[]
     */
    public function GetPropertyMappings();
    
    /**
     * @return boolean
     */
    public function HasPropertyMapping($PropertyIdentifier);
    
    /**
     * @return IPropertyMapping|null
     */
    public function GetPropertyMapping($PropertyIdentifier);
    
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
    public function ResultRow($ColumnData = []);
    
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
     * Gets the sources that must be included for every select/update/delete.
     * 
     * @param Relational\Database $Database The database of the criteria
     * @return Relational\ResultSetSources
     */
    public function GetSelectSources();
    
    /**
     * Get the criteria that must be included for every select/update/delete.
     * 
     * @param Relational\Database $Database The database of the criteria
     * @return Relational\Criteria
     */
    public function GetSelectCriteria();
    
    /**
     * Gets the select with the required sources and criteria.
     * 
     * @param Relational\Database $Database The database of the criteria
     * @return Relational\ResultSetSelect
     */
    public function GetEntitySelect();
    
    /**
     * Adds the select sources and criteria to the supplied result set specification.
     * 
     * @param Relational\ResultSetSpecification $ResultSetSpecification
     * @return void
     */
    public function AddEntityToResultSet(Relational\ResultSetSpecification $ResultSetSpecification);
    
    /**
     * Maps the supplied properties so they will be loaded in the given select.
     * 
     * @param Relational\ResultSetSelect $Select The select to add to
     * @param Object\IProperty[] $AlreadyKnownProperties The array of properties to not map
     * @return void
     */
    public function MapPropetiesToSelect(Relational\ResultSetSelect $Select, array $AlreadyKnownProperties = []);
    
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
    
    /**
     * @access private
     * 
     * Maps the supplied result rows to an array of revival data.
     * NOTE: Array keys are preserved.
     * 
     * @param Relational\ResultRows[] $ResultRows The result row to map
     * @return Object\RevivalData[]
     */
    public function MapResultRowsToRevivalData(array $ResultRowArray);
    
    /**
     * @access private
     * 
     * Maps the supplied result rows data (no relationships) to an array of revival data.
     * NOTE: Array keys are preserved.
     * 
     * @param Relational\ResultRows[] $ResultRows The result row to map
     * @return Object\RevivalData[]
     */
    public function MapResultRowDataToRevivalData(array $ResultRowArray);
}

?>