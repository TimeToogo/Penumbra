<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Containers\Map;
use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

class PropertyArrayMapping extends PropertyRelationMapping implements IPropertyCollectionMapping {
    private $ToManyRelation;
    private $RelatedArrayCache = array();
    
    public function __construct( 
            Object\IProperty $Property, 
            $EntityType,
            Relational\IToManyRelation $ToManyRelation) {
        parent::__construct($Property, $EntityType, $ToManyRelation);
        
        $this->ToManyRelation = $ToManyRelation;
    }
    
    /**
     * @return Relational\IToManyRelation
     */
    final protected function GetToManyRelation() {
        return $this->ToManyRelation;
    }
    
    protected function ReviveProperties(
            Map $ResultRowStateMap, 
            Map $ParentRelatedRowsMap, 
            Map $RelatedRowEntityMap) {
        $Property = $this->GetProperty();
        foreach($ResultRowStateMap as $ParentResultRow) {
            $State = $RelatedRowEntityMap[$ParentResultRow];
            $RelatedRows = $ParentRelatedRowsMap[$ParentResultRow];
            $Entities = array();
            foreach($RelatedRows as $RelatedRow) {
                $Entities[] = $RelatedRowEntityMap[$RelatedRow];
            }
            $this->RelatedArrayCache[$State->GetIdentity()->Hash()] = $Entities;
            $State[$Property] = $Entities;
        }
    }
    
    public function Persist(Mapping\PersistingContext $Context, Mapping\TransactionalContext $TransactionalContext) {
        $State = $Context->GetState();
        $RelatedEntityArray = $State[$this->GetProperty()];
        if(!is_array($RelatedEntityArray)) {
            //TODO: ERROR
            throw new Exception;
        }
        $IdentityHash = $State->GetIdentity()->Hash();
        $OriginalArray = isset($this->RelatedArrayCache[$IdentityHash]) ?
                $this->RelatedArrayCache[$IdentityHash] : null;
        
        $PersistedRelatedRows = array();
        $DiscardedRelatedPrimaryKeys = array();
        if($OriginalArray === null) {
            $PersistedRelatedRows = 
                    $TransactionalContext->PersistAll($RelatedEntityArray);
        }
        else {
            $Domain = $Context->GetDomainDatabaseMap()->GetDomain();
            $this->EntityArrayToHashMap($Domain, $OriginalArray);
            $this->EntityArrayToHashMap($Domain, $RelatedEntityArray);
            
            $PersistedRelatedRows = $TransactionalContext->PersistAll(array_diff_key($RelatedEntityArray, $OriginalArray));
            $DiscardedRelatedPrimaryKeys = $TransactionalContext->DiscardAll(array_diff_key($OriginalArray, $RelatedEntityArray));
        }
        
        $this->ToManyRelation->Persist($TransactionalContext->GetTransaction(), $Context->GetColumnData(), 
                $PersistedRelatedRows, $DiscardedRelatedPrimaryKeys);
    }
    
    private function EntityArrayToHashMap(Object\Domain $Domain, array &$EntityArray) {
        foreach($EntityArray as $Key => $Entity) {
            unset($EntityArray[$Key]);
            $EntityArray[$Domain->State($Entity)->Hash()] = $Entity;
        }
    }
    
    public function Discard(Mapping\DiscardingContext $Context, Mapping\TransactionalContext $TransactionalContext) {
        $RelatedEntityCollection = $Context->Get()[$this->GetProperty()];
        if(!($RelatedEntityCollection instanceof Collections\ICollection)) {
            //TODO: ERROR
            throw new Exception;
        }
        $OrginalEntities = $RelatedEntityCollection->__GetOriginalEntities();
        $DiscardedPrimaryKeys = $TransactionalContext->DiscardAll($OrginalEntities);
        $this->ToManyRelation->Discard($TransactionalContext->GetTransaction(), $Context->GetPrimaryKey(), $DiscardedPrimaryKeys);
    }
}

?>