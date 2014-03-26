<?php

namespace Storm\Drivers\Base\Mapping\Mappings;

use \Storm\Core\Mapping\IEntityPropertyToOneRelationMapping;
use \Storm\Core\Mapping\UnitOfWorkTransactionMapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class EntityPropertyToOneRelationMapping extends RelationshipPropertyRelationMapping implements IEntityPropertyToOneRelationMapping {
    private $EntityProperty;
    private $ToOneRelation;
    private $Loading;
    
    public function __construct(
            Object\IEntityProperty $EntityProperty, 
            Relational\IToOneRelation $ToOneRelation,
            Loading\IEntityLoading $Loading) {
        $Loading->VerifyCompatibility($EntityProperty);
        
        parent::__construct($EntityProperty, $ToOneRelation);
        
        $this->EntityProperty = $EntityProperty;
        $this->ToOneRelation = $ToOneRelation;
        $this->Loading = $Loading;
    }

    /**
     * @return Object\IEntityProperty
     */
    final public function GetEntityProperty() {
        return $this->EntityProperty;
    }
    /**
     * @return Relational\IToOneRelation
     */
    final public function GetToOneRelation() {
        return $this->ToOneRelation;
    }    
    
    /**
     * @return Loading\IEntityLoading
     */
    final public function GetLoading() {
        return $this->Loading;
    }
    
    final public function SetLoading(Loading\IEntityLoading $Loading) {
        $this->Loading = $Loading;
    }
    
    final public function AddLoadingRequirementsToSelect(Relational\ResultSetSelect $RelationalRequest) {
        return $this->Loading->AddLoadingRequirementsToSelect(
                $this->EntityRelationalMap, 
                $this->ToOneRelation, 
                $RelationalRequest);
    }
    
    public function AddTraversalRequirementsToResultSet(Relational\ResultSetSpecification $ResultSetSpecification) {
        $this->ToOneRelation->AddRelationToResultSet($ResultSetSpecification);
        $this->EntityRelationalMap->AddEntityToResultSet($ResultSetSpecification);
    }
    
    final public function Revive(Relational\Database $Database, array $ResultRowArray, array $RevivalDataArray) {
        $LoadingValues = $this->Loading->Load(
                $this->EntityRelationalMap, 
                $Database, 
                $this->ToOneRelation, 
                $ResultRowArray);
        foreach ($RevivalDataArray as $Key => $RevivalData) {
            $RevivalData[$this->Property] = $LoadingValues[$Key];
        }
    }
    
    public function Persist(UnitOfWorkTransactionMapping $Mapping, Relational\ResultRow $ParentData, Object\RelationshipChange $RelationshipChange) {
        if($RelationshipChange->HasDiscardedIdentity() || $RelationshipChange->HasPersistedEntityData()) {
            
            $DiscardedPrimaryKey = null;
            if($RelationshipChange->HasDiscardedIdentity()) {
                $DiscardedPrimaryKey = $this->EntityRelationalMap->MapIdentityToPrimaryKey($RelationshipChange->GetDiscardedIdentity());
            }
            
            $RelatedData = null;
            if($RelationshipChange->IsDependent()) {
                $RelatedData = $Mapping->MapPersistenceData($this->EntityRelationalMap, $RelationshipChange->GetPersistedEntityData());
            }
            else if($RelationshipChange->HasPersistedEntityData()) {
                $RelatedData = $this->EntityRelationalMap->MapIdentityToPrimaryKey($RelationshipChange->GetPersistedEntityData());
            }
            
            $this->ToOneRelation->Persist(
                    $Mapping->GetTransaction(),
                    $ParentData, 
                    $DiscardedPrimaryKey, 
                    $RelatedData);
        }
    }

    public function MapPropertyExpression(Relational\ResultSetSources $Sources, &$ReturnType) {
        throw new \Storm\Core\Mapping\MappingException();
    }
}

?>