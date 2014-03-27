<?php

namespace Penumbra\Drivers\Base\Mapping\Mappings;

use \Penumbra\Core\Mapping\ICollectionPropertyToManyRelationMapping;
use \Penumbra\Core\Mapping\UnitOfWorkTransactionMapping;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;

class CollectionPropertyToManyRelationMapping extends RelationshipPropertyRelationMapping implements ICollectionPropertyToManyRelationMapping {
    private $CollectionProperty;
    private $ToManyRelation;
    private $Loading;
    
    public function __construct(
            Object\ICollectionProperty $CollectionProperty, 
            Relational\IToManyRelation $ToManyRelation,
            Loading\ICollectionLoading $Loading) {
        $Loading->VerifyCompatibility($CollectionProperty);
        
        parent::__construct($CollectionProperty, $ToManyRelation);
        
        $this->CollectionProperty = $CollectionProperty;
        $this->ToManyRelation = $ToManyRelation;
        $this->Loading = $Loading;
    }

    /**
     * @return Object\ICollectionProperty
     */
    final public function GetCollectionProperty() {
        return $this->CollectionProperty;
    }
    
    /**
     * @return Relational\IToManyRelation
     */
    final public function GetToManyRelation() {
        return $this->ToManyRelation;
    }
    
    /**
     * @return Loading\ICollectionLoading
     */
    final public function GetLoading() {
        return $this->Loading;
    }
    
    final public function SetLoading(Loading\ICollectionLoading $Loading) {
        $this->Loading = $Loading;
    }
    
    final public function AddLoadingRequirementsToSelect(Relational\ResultSetSelect $Select) {
        return $this->Loading->AddLoadingRequirementsToSelect(
                $this->EntityRelationalMap, 
                $this->ToManyRelation, 
                $Select);
    }
    
    public function AddTraversalRequirementsToResultSet(Relational\ResultSetSpecification $ResultSetSpecification) {
        throw new \Exception();
    }
    
    final public function Revive(Relational\Database $Database, array $ResultRowArray, array $RevivalDataArray) {
        $LoadingValues = $this->Loading->Load(
                $this->EntityRelationalMap, 
                $Database, 
                $this->ToManyRelation, 
                $ResultRowArray);
        foreach ($RevivalDataArray as $Key => $RevivalData) {
            $RevivalData[$this->Property] = $LoadingValues[$Key];
        }
    }
    
    public function Persist(UnitOfWorkTransactionMapping $Mapping, Relational\ResultRow $ParentData, array $RelationshipChanges) {
        if(count($RelationshipChanges) === 0) {
            return;
        } 
        
        $DiscardedIdentities = [];
        $DependentPersistenceDataArray = [];
        $RelatedIdentities= [];
        
        foreach ($RelationshipChanges as $Key => $RelationshipChange) {
            if($RelationshipChange->HasDiscardedIdentity()) {
                $DiscardedIdentities[$Key] = $RelationshipChange->GetDiscardedIdentity();
            }

            if($RelationshipChange->IsDependent()) {
                $DependentPersistenceDataArray[$Key] = $RelationshipChange->GetPersistedEntityData();
            }
            else if($RelationshipChange->HasPersistedEntityData()) {
                $RelatedIdentities[$Key] = $RelationshipChange->GetPersistedEntityData();
            }
        }
        
        $NullKeys = array_fill_keys(array_keys($RelationshipChanges), null);
        $DiscardedPrimaryKeys = $this->EntityRelationalMap->MapIdentitiesToPrimaryKeys($DiscardedIdentities) + $NullKeys;
        
        $RelatedDepedenctResultRows = $Mapping->MapPersistenceDataArray($this->EntityRelationalMap, $DependentPersistenceDataArray);
        
        $PersistedRelatedDataArray = 
                $RelatedDepedenctResultRows + 
                $this->EntityRelationalMap->MapIdentitiesToPrimaryKeys($RelatedIdentities) +
                $NullKeys;
        
        $this->ToManyRelation->Persist(
                $Mapping->GetTransaction(), 
                $ParentData, 
                $DiscardedPrimaryKeys, 
                $PersistedRelatedDataArray);
    }
    
    public function MapPropertyExpression(Relational\ResultSetSources $Sources, &$ReturnType) {
        throw new \Penumbra\Core\Mapping\MappingException();
    }
}

?>