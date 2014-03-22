<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Mapping\ICollectionPropertyToManyRelationMapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

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
    
    public function Persist(Relational\Transaction $Transaction, Relational\ResultRow $ParentData, array $RelationshipChanges) {
        if(count($RelationshipChanges) === 0) {
            return;
        } 
        
        $DiscardedPrimaryKeys = [];
        $PersistedRelatedDataArray = [];
        
        foreach ($RelationshipChanges as $Key => $RelationshipChange) {
            
            if($RelationshipChange->HasDiscardedIdentity() || $RelationshipChange->HasPersistedEntityData()) {
                $DiscardedPrimaryKeys[$Key] = null;
                if($RelationshipChange->HasDiscardedIdentity()) {
                    $DiscardedPrimaryKeys[$Key] = $this->EntityRelationalMap->MapIdentityToPrimaryKey($RelationshipChange->GetDiscardedIdentity());
                }
                
                $PersistedRelatedDataArray[$Key] = null;
                if($RelationshipChange->IsDependent()) {
                    $PersistedRelatedDataArray[$Key] = $this->EntityRelationalMap->MapPersistenceDataToResultRows($Transaction, [$RelationshipChange->GetPersistedEntityData()])[0];
                    $Transaction->PersistAll($PersistedRelatedDataArray[$Key]->GetRows());
                }
                else if($RelationshipChange->HasPersistedEntityData()) {
                    $PersistedRelatedDataArray[$Key] = $this->EntityRelationalMap->MapIdentityToPrimaryKey($RelationshipChange->GetPersistedEntityData());
                }
            }
        }
        
        $this->ToManyRelation->Persist(
                $Transaction, 
                $ParentData, 
                $DiscardedPrimaryKeys, 
                $PersistedRelatedDataArray);
    }
    
    public function MapPropertyExpression(Relational\ResultSetSources $Sources, &$ReturnType) {
        throw new \Storm\Core\Mapping\MappingException();
    }
}

?>