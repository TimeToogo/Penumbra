<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Containers\Registrar;
use \Storm\Core\Object;
use \Storm\Core\Object\IProperty;
use \Storm\Core\Relational;
use \Storm\Core\Relational\IColumn;

abstract class EntityRelationalMap implements IEntityRelationalMap {
    use \Storm\Core\Helpers\Type;    
    
    /**
     * @var Object\EntityMap
     */
    private $EntityMap;
    private $EntityType;
    
    /**
     * @var Relational\Table
     */
    private $PrimaryKeyTable;
    /**
     * @var Relational\Table[]
     */
    private $PersistTables;
    /**
     * @var Relational\Table[]
     */
    private $ReviveTables;
    
    /**
     * @var IProperty[]
     */
    private $MappedProperties = array();
    
    /**
     * @var Relational\IColumn[]
     */
    private $MappedReviveColumns = array();
    /**
     * @var Relational\IColumn[]
     */
    private $MappedPersistColumns = array();
    
    /**
     * @var IPropertyMapping
     */
    private $PropertyMappings = array();
    /**
     * @var IDataPropertyColumnMapping[]
     */
    private $DataPropertyColumnMappings = array();
    /**
     * @var IDataPropertyColumnMapping[]
     */
    private $IdentityPropertyPrimaryKeyMappings = array();
    /**
     * @var IEntityPropertyToOneRelationMapping[]
     */
    private $EntityPropertyToOneRelationMappings = array();
    /**
     * @var ICollectionPropertyToManyRelationMapping[]
     */
    private $CollectionPropertyToManyRelationMappings = array();
    
    final public function Initialize(DomainDatabaseMap $DomainDatabaseMap) {
        $this->OnInitialize($DomainDatabaseMap);
        
        $this->EntityMap = $this->EntityMap($DomainDatabaseMap->GetDomain());
        $this->EntityType = $this->EntityMap->GetEntityType();
        if(!($this->EntityMap instanceof Object\EntityMap))
            throw new \UnexpectedValueException
                    ('Return value from ' . get_class($this) . '->EntityMap() must be a valid EntityMap');
        
        $Database = $DomainDatabaseMap->GetDatabase();
        $this->PrimaryKeyTable = $this->PrimaryKeyTable($Database);
        if(!($this->PrimaryKeyTable instanceof Relational\Table))
            throw new \UnexpectedValueException
                    ('Return value from ' . get_class($this) . '->PrimaryKeyTable() must be a valid Table');
        
        $Registrar = new Registrar(IPropertyMapping::IPropertyMappingType);
        $this->RegisterPropertyMappings($Registrar, $this->EntityMap, $Database);
        
        foreach($Registrar->GetRegistered() as $PropertyMapping) {
            $this->AddPropertyMapping($PropertyMapping);
        }
        foreach($this->MappedReviveColumns as $MappedReviveColumn) {
            $Table = $MappedReviveColumn->GetTable();
            $this->ReviveTables[$Table->GetName()] = $Table;
        }
        foreach($this->MappedPersistColumns as $MappedPersistColumn) {
            $Table = $MappedPersistColumn->GetTable();
            $this->PersistTables[$Table->GetName()] = $Table;
        }
        
        
        $this->OnInitialized($DomainDatabaseMap);
    }
    protected function OnInitialize(DomainDatabaseMap $DomainDatabaseMap) { }
    protected function OnInitialized(DomainDatabaseMap $DomainDatabaseMap) { }
    
    protected abstract function EntityMap(Object\Domain $Domain);
    protected abstract function PrimaryKeyTable(Relational\Database $Database);
    protected abstract function RegisterPropertyMappings(Registrar $Registrar, Object\EntityMap $EntityMap, Relational\Database $Database);
    
    private function AddPropertyMapping(IPropertyMapping $PropertyMapping) {
        $ProperyIdentifier = $PropertyMapping->GetProperty()->GetIdentifier();
        if($PropertyMapping instanceof IDataPropertyColumnMapping) {
            $this->DataPropertyColumnMappings[$ProperyIdentifier] = $PropertyMapping;
            $this->MappedReviveColumns = array_merge($this->MappedReviveColumns, $PropertyMapping->GetReviveColumns());
            $this->MappedPersistColumns = array_merge($this->MappedPersistColumns, $PropertyMapping->GetPersistColumns());
            if($PropertyMapping->IsIdentityPrimaryKeyMapping()) {
                $this->IdentityPropertyPrimaryKeyMappings[] = $PropertyMapping;
            }
        }
        else if($PropertyMapping instanceof IEntityPropertyToOneRelationMapping) {
            $this->EntityPropertyToOneRelationMappings[$ProperyIdentifier] = $PropertyMapping;
        }
        else if($PropertyMapping instanceof ICollectionPropertyToManyRelationMapping) {
            $this->CollectionPropertyToManyRelationMappings[$ProperyIdentifier] = $PropertyMapping;
        }
        else {
            throw new \UnexpectedValueException('$PropertyMapping not instance of ^');//TODO: error messages
        }
        $this->MappedProperties[$ProperyIdentifier] = $PropertyMapping->GetProperty();
        $this->PropertyMappings[$ProperyIdentifier] = $PropertyMapping;
    }
    
    final public function GetEntityMap() {
        return $this->EntityMap;
    }
    
    final public function GetPrimaryKeyTable() {
        return $this->PrimaryKeyTable;
    }
    
    final public function GetMappedReviveTables() {
        return $this->ReviveTables;
    }
    
    final public function GetMappedPersistTables() {
        return $this->PersistTables;
    }
    
    final public function GetEntityType() {
        return $this->EntityType;
    }
    
    final public function GetPropertyMappings() {
        return $this->PropertyMappings;
    }
    
    final public function GetDataPropertyColumnMappings() {
        return $this->DataPropertyColumnMappings;
    }
    
    final public function GetIdentityPropertyPrimaryKeyMappings() {
        return $this->IdentityPropertyPrimaryKeyMappings;
    }
    
    final public function GetEntityPropertyToOneRelationMappings() {
        return $this->EntityPropertyToOneRelationMappings;
    }
    
    final public function GetCollectionPropertyToManyRelationMappings() {
        return $this->CollectionPropertyToManyRelationMappings;
    }
    
    public function ResultRow($ColumnData = array()) {
        return new Relational\ResultRow($this->MappedPersistColumns, $ColumnData);
    }

    final public function GetCriterion() {
        $RelationalCriterion = new Relational\Criterion();
        $RelationalCriterion->AddTables($this->GetMappedReviveTables());
        $this->RelationalCriterion($RelationalCriterion);
        
        return $RelationalCriterion;
    }
    protected function RelationalCriterion(Relational\Criterion $RelationalCriterion) { }
    
    private function VerifyDataPropertyColumnMapping(IProperty $Property) {
        $PropertyIdentifier = $Property->GetIdentifier();
        if(isset($this->DataPropertyColumnMappings[$PropertyIdentifier])) {
            return $this->DataPropertyColumnMappings[$PropertyIdentifier];
        }
        else {
            throw new \Storm\Core\Exceptions\UnmappedPropertyException();
        }
    }
    
    final public function GetMappedReviveColumns(IProperty $Property) {
        return $this->VerifyDataPropertyColumnMapping($Property)->GetReviveColumns();
    }
    
    final public function GetMappedPersistColumns(IProperty $Property) {
        return $this->VerifyDataPropertyColumnMapping($Property)->GetPersistColumns();
    }
    
    final public function GetAllMappedReviveColumns(array $Properties = null) {
        if($Properties === null) {
            return $this->MappedReviveColumns;
        }
        $ColumnGroups = array_map([$this, 'GetMappedReviveColumns'], $Properties);
        $Columns = call_user_func_array('array_merge', $ColumnGroups);
        return $Columns;
    }
    
    final public function GetAllMappedPersistColumns(array $Properties = null) {
        if($Properties === null) {
            return $this->MappedPersistColumns;
        }
        $ColumnGroups = array_map([$this, 'GetMappedPersistColumns'], $Properties);
        $Columns = call_user_func_array('array_merge', $ColumnGroups);
        return $Columns;
    }

    final public function MapIdentityToPrimaryKey(Object\Identity $Identity) {
        $PrimaryKey = $this->PrimaryKeyTable->PrimaryKey();
        foreach($this->IdentityPropertyPrimaryKeyMappings as $Mapping) {
            $Property = $Mapping->GetProperty();
            $PropertyValue = $Identity[$Property];
            if(isset($Identity[$Property])) {
                $Mapping->Persist($PropertyValue, $PrimaryKey);
            }
        }
        
        return $PrimaryKey;
    }
    final public function MapIdentitiesToPrimaryKeys(array $Identities) {
        return array_map([$this, 'MapIdentityToPrimaryKey'], $Identities);
    }
    
    final public function MapPrimaryKeyToIdentity(Relational\PrimaryKey $PrimaryKey) {
        $Identity = $this->EntityMap->Identity();
        $Map = Map::From([$PrimaryKey], [$Identity]);
        $this->RevivePrimaryKeyIdentityMap($Map);
        
        return $Identity;
    }
    final public function MapPrimaryKeysToIdentities(array $PrimaryKeys) {
        $Identities = array();
        foreach($PrimaryKeys as $Key => $PrimaryKey) {
            $Identities[$Key] = $this->EntityMap->Identity();
        }
        $Map = Map::From([$PrimaryKeys], [$Identities]);
        $this->RevivePrimaryKeyIdentityMap($Map);
        
        return $Identities;
    }
    private function RevivePrimaryKeyIdentityMap(Map $Map) {
        foreach($this->IdentityPropertyPrimaryKeyMappings as $Mapping) {
            $Mapping->Revive($Map);
        }
    }
}

?>