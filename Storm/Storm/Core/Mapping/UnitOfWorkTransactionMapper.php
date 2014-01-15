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

    
    // <editor-fold defaultstate="collapsed" desc="Entity Persistence Helpers">

    private function MapUnitOfWorkToTransaction(
            Object\UnitOfWork $UnitOfWork, 
            Relational\Transaction $Transaction) {
        foreach($UnitOfWork->GetPersistenceDataGroups() as $EntityType => $PersistenceDataGroup) {
            $this->MapPersistenceDataToTransaction($UnitOfWork, $Transaction, $PersistenceDataGroup);
        }
        foreach($UnitOfWork->GetExecutedProcedures() as $Procedure) {
            $Transaction->Execute($this->MapProcedure($Procedure));
        }
        foreach($UnitOfWork->GetDiscardenceDataGroups() as $EntityType => $DiscardedIdentityGroup) {
            $EntityRelationalMap = $this->EntityRelationMaps[$EntityType];
            $ResultRows = $this->MapEntityDataToTransaction($UnitOfWork, $Transaction, $EntityRelationalMap, $DiscardedIdentityGroup);
            foreach($ResultRows as $ResultRow) {
                $Transaction->DiscardAll($ResultRow->GetPrimaryKeys());
            }            
        }
        foreach($UnitOfWork->GetDiscardedCriteria() as $DiscardedCriterion) {
            $Transaction->DiscardWhere($this->MapObjectCriterion($DiscardedCriterion));
        }
    }
    
    private function MapPersistenceDataToTransaction(
            Object\UnitOfWork $UnitOfWork, 
            Relational\Transaction $Transaction,
            array $PersistenceDataArray) {
        if(count($PersistenceDataArray) === 0) {
            return;
        }
        
        $EntityRelationalMap = $this->EntityRelationMaps[reset($PersistenceDataArray)->GetEntityType()];
        $PrimaryKeyTable = $EntityRelationalMap->GetPrimaryKeyTable();
        $ResultRows = $this->MapEntityDataToTransaction($UnitOfWork, $Transaction, $EntityRelationalMap, $PersistenceDataArray);
        
        foreach($ResultRows as $Key => $ResultRow) {
            $PersistenceData = $PersistenceDataArray[$Key];
            $Transaction->PersistAll($ResultRow->GetRows());
            
            $PrimaryKeyRow = $ResultRow->GetRow($PrimaryKeyTable);
            if(!$PrimaryKeyRow->HasPrimaryKey()) {
                $Transaction->SubscribeToPostPersistEvent(
                        $ResultRow->GetRow($PrimaryKeyTable), 
                        function ($PersistedRow) use (&$UnitOfWork, $PersistenceData, &$EntityRelationalMap) {
                            $Identity = $EntityRelationalMap->MapPrimaryKeyToIdentity($PersistedRow->GetPrimaryKey());
                            $UnitOfWork->SupplyIdentity($PersistenceData, $Identity);
                        });
            }
        }
        
        return $ResultRows;
    }
    
    private function MapEntityDataToTransaction(
            Object\UnitOfWork $UnitOfWork, Relational\Transaction $Transaction, 
            IEntityRelationalMap $EntityRelationalMap, array $EntityDataArray) {
        
        $DataPropertyColumnMappings = $EntityRelationalMap->GetDataPropertyColumnMappings();
        $EntityPropertyToOneRelationMappings = $EntityRelationalMap->GetEntityPropertyToOneRelationMappings();
        $CollectionPropertyToManyRelationMappings = $EntityRelationalMap->GetCollectionPropertyToManyRelationMappings();
        
        $ResultRowArray = array();
        foreach($EntityDataArray as $Key => $EntityData) {
            $ResultRowData = $EntityRelationalMap->ResultRow();
            
            foreach($DataPropertyColumnMappings as $DataPropertyColumnMapping) {
                $Property = $DataPropertyColumnMapping->GetProperty();
                if(isset($EntityData[$Property])) {
                    $DataPropertyValue = $EntityData[$Property];
                    $DataPropertyColumnMapping->Persist($DataPropertyValue, $ResultRowData);
                }
            }
            
            foreach($EntityPropertyToOneRelationMappings as $EntityPropertyToOneRelationMapping) {
                $RelationshipChange = $EntityData[$EntityPropertyToOneRelationMapping->GetProperty()];
                $MappedRelationshipChange = 
                        $this->MapRelationshipChanges($UnitOfWork, $Transaction, 
                        [$RelationshipChange])[0];
                $EntityPropertyToOneRelationMapping->Persist($Transaction, $ResultRowData, $MappedRelationshipChange);
            }
            
            foreach($CollectionPropertyToManyRelationMappings as $CollectionPropertyToManyRelationMapping) {
                $RelationshipChanges = $EntityData[$CollectionPropertyToManyRelationMapping->GetProperty()];
                $MappedRelationshipChanges = 
                        $this->MapRelationshipChanges($UnitOfWork, $Transaction, $RelationshipChanges);
                
                $CollectionPropertyToManyRelationMapping->Persist($Transaction, $ResultRowData, $MappedRelationshipChanges);
            }
            
            $ResultRowArray[$Key] = $ResultRowData;
        }
        
        return $ResultRowArray;
    }
    
    // </editor-fold>
    
    
    // <editor-fold defaultstate="collapsed" desc="Relationship Mapping">
    
    private function MapIdentityToPrimaryKey(Object\Identity $Identity) {
        $EntityRelationalMap = $this->EntityRelationMaps[$Identity->GetEntityType()];
        return $EntityRelationalMap->MapIdentityToPrimaryKey($Identity);
    }
    
    private function MapPrimaryKeyToIdentity(Relational\PrimaryKey $PrimaryKey) {
        $EntityRelationalMap = $this->GetRelationMapByPrimaryKeyTable($PrimaryKey->GetTable()->GetName());
        return $EntityRelationalMap->MapPrimaryKeyToIdentity($PrimaryKey);
    }
    
    /**
     * @internal
     * @return Relational\DiscardedRelationship
     */
    final public function MapDiscardedRelationships(array $ObjectDiscardedRelationships) {
        $RelationalDiscardedRelationships = array();
        foreach($ObjectDiscardedRelationships as $Key => $DiscardedRelationship) {
            if($DiscardedRelationship === null) {
                $RelationalDiscardedRelationships[$Key] = null;
                continue;
            }
            $ParentPrimaryKey = $this->MapIdentityToPrimaryKey($DiscardedRelationship->GetParentIdentity());
            $ChildPrimaryKey = $this->MapIdentityToPrimaryKey($DiscardedRelationship->GetRelatedIdentity());
            
            $RelationalDiscardedRelationships[$Key] = new Relational\DiscardedRelationship($ParentPrimaryKey, $ChildPrimaryKey);
        }
        
        return $RelationalDiscardedRelationships; 
    }


    /**
     * @internal
     * @return Relational\PersistedRelationship
     */
    final public function MapPersistedRelationships(
            Object\UnitOfWork $UnitOfWork, Relational\Transaction $Transaction,             
            array $ObjectPersistedRelationships) {
        
        $ParentPrimaryKey = null;
        $ChildPersistenceData = array();
        foreach($ObjectPersistedRelationships as $Key => $ObjectPersistedRelationship) {
            if($ObjectPersistedRelationship === null) {
                continue;
            }
            if($ParentPrimaryKey === null) {
                $ParentPrimaryKey = $this->MapIdentityToPrimaryKey(
                        $ObjectPersistedRelationship->GetParentIdentity());
            }            
            if ($ObjectPersistedRelationship->IsIdentifying()) {
                $ChildPersistenceData[$Key] = $ObjectPersistedRelationship->GetChildPersistenceData();
            }
        }
        $ChildResultRows = $this->MapPersistenceDataToTransaction($UnitOfWork, $Transaction, $ChildPersistenceData);
        

        $RelationalPersistedRelationships = array();
        foreach($ObjectPersistedRelationships as $Key => $ObjectPersistedRelationship) {
            if($ObjectPersistedRelationship === null) {
                $RelationalPersistedRelationships[$Key] = null;
                continue;
            }
            if ($ObjectPersistedRelationship->IsIdentifying()) {
                $RelationalPersistedRelationships[$Key] = 
                        new Relational\PersistedRelationship($ParentPrimaryKey, null, $ChildResultRows[$Key]);
            }
            else {
                $RelatedPrimaryKey = $this->MapIdentityToPrimaryKey($ObjectPersistedRelationship->GetRelatedIdentity());
                $RelationalPersistedRelationships[$Key] = 
                        new Relational\PersistedRelationship($ParentPrimaryKey, $RelatedPrimaryKey, null);
            }
        }
        
        return $RelationalPersistedRelationships;
    }


    /**
     * @internal
     * @return Relational\RelationshipChange
     */
    final public function MapRelationshipChanges(
            Object\UnitOfWork $UnitOfWork, Relational\Transaction $Transaction,
            array $ObjectRelationshipChanges) {
        
        $ObjectPersistedRelationships = array();
        $ObjectDiscardedRelationships = array();
        
        foreach($ObjectRelationshipChanges as $Key => $ObjectRelationshipChange) {
            $ObjectPersistedRelationships[$Key] = $ObjectRelationshipChange->GetPersistedRelationship();
            $ObjectDiscardedRelationships[$Key] = $ObjectRelationshipChange->GetDiscardedRelationship();
        }
        
        $RelationalPersistedRelationships = $this->MapPersistedRelationships($UnitOfWork, $Transaction, 
                $ObjectPersistedRelationships);
        $RelationalDiscardedRelationships = $this->MapDiscardedRelationships($ObjectDiscardedRelationships);
        
        $RelationalRelationshipChanges = array();
        foreach($ObjectRelationshipChanges as $Key => $ObjectRelationshipChange) {
            $RelationalRelationshipChanges[$Key] = new Relational\RelationshipChange(
                    $RelationalPersistedRelationships[$Key], $RelationalDiscardedRelationships[$Key]);
        }
        
        return $RelationalRelationshipChanges;
    }

    // </editor-fold>
}

?>