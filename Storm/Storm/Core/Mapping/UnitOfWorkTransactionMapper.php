<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Core\Containers\Registrar;
use \Storm\Core\Containers\Map;

final class UnitOfWorkTransactionMapper {
    /**
     * @var IEntityRelationalMap[] 
     */
    private $EntityRelationMaps;
    public function __construct(array $EntityRelationMaps) {
        $this->EntityRelationMaps = $EntityRelationMaps;
    }

    
    public function MapUnitOfWorkToTransaction(Object\UnitOfWork $UnitOfWork, Relational\Transaction $Transaction) {
        foreach($UnitOfWork->GetPersistenceDataGroups() as $EntityType => $PersistenceDataGroup) {
            $EntityRelationalMap = $this->EntityRelationMaps[$EntityType];
            $ResultRows = $this->MapEntityDataToTransaction($Transaction, $EntityRelationalMap, $PersistenceDataGroup);
            foreach($ResultRows as $ResultRow) {
                $Transaction->PersistAll($ResultRow->GetRows());
            }
        }
        foreach($UnitOfWork->GetExecutedProcedures() as $Procedure) {
            $Transaction->Execute($this->MapProcedure($Procedure));
        }
        foreach($UnitOfWork->GetDiscardenceDataGroups() as $EntityType => $DiscardedIdentityGroup) {
            $EntityRelationalMap = $this->EntityRelationMaps[$EntityType];
            $ResultRows = $this->MapEntityDataToTransaction($Transaction, $EntityRelationalMap, $DiscardedIdentityGroup);
        }
        foreach($UnitOfWork->GetDiscardedCriteria() as $DiscardedRequest) {
            $Transaction->DiscardWhere($this->MapRequest($DiscardedRequest));
        }
    }
    
    private function MapEntityDataToTransaction(Relational\Transaction $Transaction, 
            IEntityRelationalMap $EntityRelationalMap, array $EntityDataArray) {
        
        $DataPropertyColumnMappings = $EntityRelationalMap->GetDataPropertyColumnMappings();
        $EntityPropertyToOneRelationMappings = $EntityRelationalMap->GetEntityPropertyToOneRelationMappings();
        $CollectionPropertyToManyRelationMappings = $EntityRelationalMap->GetCollectionPropertyToManyRelationMappings();
        
        $ResultRowArray = array();
        foreach($EntityDataArray as $EntityData) {
            $ResultRowData = $EntityRelationalMap->ResultRow();
            
            foreach($DataPropertyColumnMappings as $DataPropertyColumnMapping) {
                $Property = $DataPropertyColumnMapping->GetProperty();
                if(isset($EntityData[$Property])) {
                    $DataPropertyValue = $EntityData[$Property];
                    $DataPropertyColumnMapping->Persist($DataPropertyValue, $ResultRowData);
                }
            }
            
            foreach($EntityPropertyToOneRelationMappings as $EntityPropertyToOneRelationMapping) {
                $MappedRelationshipChange = 
                        $this->MapRelationshipChange($Transaction, $EntityData[$EntityPropertyToOneRelationMapping->GetProperty()]);
                $EntityPropertyToOneRelationMapping->Persist($Transaction, $MappedRelationshipChange);
            }
            
            foreach($CollectionPropertyToManyRelationMappings as $CollectionPropertyToManyRelationMapping) {
                $MappedRelationshipChanges = array();
                foreach($EntityData[$EntityPropertyToOneRelationMapping->GetProperty()] as $RelationshipChange) {
                    $MappedRelationshipChanges[] = $this->MapRelationshipChange($Transaction, $RelationshipChange);
                }
                $CollectionPropertyToManyRelationMapping->Persist($Transaction, $MappedRelationshipChanges);
            }
            
            $ResultRowArray[] = $ResultRowData;
        }
        
        return $ResultRowArray;
    }
    
    // <editor-fold defaultstate="collapsed" desc="Relationship Mapping Helpers">
    
    private function MapIdentityToPrimaryKey(Object\Identity $Identity) {
        $EntityRelationalMap = $this->EntityRelationMaps[$Identity->GetEntityType()];
        return $EntityRelationalMap->MapIdentityToPrimaryKey($Identity);
    }
    
    /**
     * @internal
     * @return Relational\DiscardedRelationship
     */
    final public function MapDiscardedRelationship(Object\DiscardedRelationship $DiscardedRelationship) {        
        $ParentPrimaryKey = $this->MapIdentityToPrimaryKey($DiscardedRelationship->GetParentIdentity());
        $ChildPrimaryKey = $this->MapIdentityToPrimaryKey($DiscardedRelationship->GetRelatedIdentity());
        
        return new Relational\DiscardedRelationship($ParentPrimaryKey, $ChildPrimaryKey);
    }


    /**
     * @internal
     * @return Relational\PersistedRelationship
     */
    final public function MapPersistedRelationship(Relational\Transaction $Transaction,             
            Object\PersistedRelationship $PersistedRelationship) {       
        $ParentPrimaryKey = $this->MapIdentityToPrimaryKey($PersistedRelationship->GetParentIdentity());

        if ($PersistedRelationship->IsIdentifying()) {
            $ChildPersistenceData = $PersistedRelationship->GetChildPersistenceData();
            $RelatedEntityRelationalMap = $this->EntityRelationMaps[$ChildPersistenceData->GetEntityType()];
            $ChildResultRow = $this->MapEntityDataToTransaction(
                    $Transaction, $RelatedEntityRelationalMap, [$ChildPersistenceData])[0];
            
            return new Relational\PersistedRelationship($ParentPrimaryKey, null, $ChildResultRow);
        }         
        else {
            $RelatedPrimaryKey = $this->MapIdentityToPrimaryKey($PersistedRelationship->GetRelatedIdentity());
            return new Relational\PersistedRelationship($ParentPrimaryKey, $RelatedPrimaryKey, null);
        }
    }


    /**
     * @internal
     * @return Relational\RelationshipChange
     */
    final public function MapRelationshipChange(Relational\Transaction $Transaction,
            Object\RelationshipChange $ObjectRelationshipChange) {
        $PersistedRelationship = null;
        $DiscardedRelationship = null;
        if ($ObjectRelationshipChange->HasPersistedRelationship()) {
            $PersistedRelationship = $this->MapPersistedRelationship($Transaction, $ObjectRelationshipChange->GetPersistedRelationship());
        }
        if ($ObjectRelationshipChange->HasDiscardedRelationship()) {
            $DiscardedRelationship = $this->MapDiscardedRelationship($ObjectRelationshipChange->GetDiscardedRelationship());
        }


        return new Relational\RelationshipChange($PersistedRelationship, $DiscardedRelationship);
    }

    // </editor-fold>
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Entity Identification Helpers">
    final public function EnsureIdentifiable(array $Entities) {
        if(count($Entities) === 0)
            return;
        
        $EntityTypes = array_unique(array_map('get_class', $Entities));
        foreach ($EntityTypes as $EntityType) {
            $EntitiesOfType = array_filter($Entities, 
                    function ($Entity) use($EntityType) 
                    {
                        return $Entity instanceof $EntityType;
                    });
            
            $EntityRelationalMap = $this->GetRelationMap($EntityType);
            $EntityMap = $EntityRelationalMap->GetEntityMap();
            
            $IdentityProperties = $EntityMap->GetIdentityProperties();
            $PrimaryKeyColumns = $EntityRelationalMap->GetAllMappedReviveColumns($IdentityProperties);
            $Table = null;
            foreach($PrimaryKeyColumns as $Column) {
                if($Table === null) {
                    $Table = $Column->GetTable();
                }
                else if(!$Column->GetTable()->Is($Table)) {
                    throw new \Exception('Identity properties cannot map to columns across multiple tables');
                }
            }
            
            $UnidentifiableEntities = $this->GetUnidentifiable($EntityMap, $EntitiesOfType);
            
            $NewPrimaryKeys = $this->Database->GeneratePrimaryKeys($Table, count($UnidentifiableEntities));
            
            $NewIdentities = array_fill_keys(array_keys($NewPrimaryKeys), null);
            array_walk($NewIdentities, 
                    function (&$Value, $Key) use (&$EntityMap) {
                        $Value = $EntityMap->Identity();
                    });
                    
            array_walk($NewPrimaryKeys, 
                    function ($PrimaryKey, $Key) use (&$NewIdentities, &$EntityRelationalMap) {
                        $EntityRelationalMap->MapColumnDataToPropertyData($PrimaryKey, $NewIdentities[$Key]);
                    });


            $Count = 0;
            $NewIdentities = array_values($NewIdentities);
            foreach ($UnidentifiableEntities as $Entity) {
                $EntityMap->Apply($Entity, $NewIdentities[$Count]);
                $Count++;
            }
        }
    
    }
    
    private function GetUnidentifiable(Object\EntityMap $EntityMap, array $Entities) {
        $UnidentifiableEntities = array();
        foreach ($Entities as $Entity) {
            if (!$EntityMap->HasIdentity($Entity)) {
                $UnidentifiableEntities[] = $Entity;
            }
        }
        
        return $UnidentifiableEntities;
    }

    final public function IsIdenitifiable($Entity) {
        return $this->Domain->HasIdentity($Entity);
    }
    
    final public function VerifyIdentifiable(array $Entities) {
        if (count(array_filter($Entities, [$this, 'IsIdenitifiable'])) !== count($Entities))
            throw new \Storm\Core\Exceptions\UnidentifiableEntityException();
    }
    // </editor-fold>
}

?>