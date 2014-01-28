<?php

namespace Storm\Core\Object;

/**
 * This unit of work contains data for taking actions against entities in a given domain.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class UnitOfWork {
    /**
     * @var Domain 
     */
    private $Domain;
    
    /**
     * @var Map 
     */
    private $PersistenceDataEntityMap;
    
    /**
     * @var \ArrayObject[] 
     */
    private $PersistenceData = array();
    
    /**
     * @var \ArrayObject[][]
     */
    private $PersistenceDataGroups = array();
    
    /**
     * @var IProcedure[] 
     */
    private $ProcedureToExecute = array();
    
    /**
     * @var array[] 
     */
    private $DiscardenceData = array();
    
    /**
     * @var array[][] 
     */
    private $DiscardedIdentityGroups = array();
    
    /**
     * @var ICriterion[] 
     */
    private $DiscardedCriteria = array();
    
    public function __construct(Domain $Domain) {
        $this->PersistenceDataEntityMap = new \Storm\Core\Containers\Map();
        $this->Domain = $Domain;
    }
    
    /**
     * @return Domain
     */
    public function GetDomain() {
        return $this->Domain;
    }
    
    /**
     * Persist an entity to the unit of work.
     * 
     * @param object $Entity The entity to persist
     * @return \ArrayObject 
     */
    public function Persist($Entity) {
        $Hash = spl_object_hash($Entity);
        if(isset($this->PersistenceData[$Hash])) {
            return $this->PersistenceData[$Hash];
        }
        
        $PersistenceData = new \ArrayObject($this->Domain->Persist($this, $Entity));
        $this->PersistenceData[$Hash] = $PersistenceData;
        $this->PersistenceDataEntityMap[$PersistenceData] = $Entity;
        
        $EntityType = $this->Domain->GetMatchingEntityType($Entity);
        if(!isset($this->PersistenceDataGroups[$EntityType])) {
            $this->PersistenceDataGroups[$EntityType] = array();
        }
        $this->PersistenceDataGroups[$EntityType][] = $PersistenceData;
        
        return $PersistenceData;
    }
    
    
    /**
     * 
     * @param \ArrayObject $PersistenceData The persistence data of the entity
     * @param object $Identity The identity to supply the entity
     */
    public function RequestIdentity(\ArrayObject $PersistenceData, $Entity) {
        $this->PersistenceDataEntityMap[$PersistenceData] = $Entity;
    }
    
    /**
     * Sets a generated identity to to the entity mapped with the supplied persistence data.
     * 
     * @param \ArrayObject $PersistenceData The persistence data of the entity
     * @param array $Identity The identity to supply the entity
     */
    public function SupplyIdentity(\ArrayObject $PersistenceData, array $Identity) {
        if(isset($this->PersistenceDataEntityMap[$PersistenceData])) {
            $this->Domain->Apply($this->PersistenceDataEntityMap[$PersistenceData], $Identity);
        }
    }
    
    /**
     * @return object[]
     */
    public function GetEntities() {
        return $this->PersistenceDataEntityMap->GetToInstances();
    }
    
    /**
     * Add a procedure to be executed.
     * 
     * @param IProcedure $Procedure The procedure to execute
     * @return void
     */
    public function Execute(IProcedure $Procedure) {
        $this->ProcedureToExecute[] = $Procedure;
    }
    
    /**
     * Discards the supplied entity 
     * 
     * @param object $Entity The entity to discard
     * @return void
     */
    public function Discard($Entity) {
        $Hash = spl_object_hash($Entity);
        if(isset($this->DiscardenceData[$Hash])) {
            return $this->DiscardenceData[$Hash];
        }
        if(!$this->Domain->HasIdentity($Entity)) {
            return;
        }
        
        $DiscardenceData = $this->Domain->Discard($this, $Entity);
        $this->DiscardenceData[$Hash] = $DiscardenceData;
        
        $EntityType = $this->Domain->GetMatchingEntityType($Entity);
        if(!isset($this->DiscardedIdentityGroups[$EntityType])) {
            $this->DiscardedIdentityGroups[$EntityType] = array();
        }
        $this->DiscardedIdentityGroups[$EntityType][] = $DiscardenceData;
    }
    
    /**
     * Discard all entities matching the supplied criterion.
     * 
     * @param ICriterion $Criterion The criterion to discard by
     */
    public function DiscardWhere(ICriterion $Criterion) {
        $this->DiscardedCriteria[spl_object_hash($Criterion)] = $Criterion;
    }
    
    /**
     * @return array[]
     */
    public function GetPersistenceData() {
        return $this->PersistenceData;
    }
    
    /**
     * @return array[][]
     */
    public function GetPersistenceDataGroups() {
        return $this->PersistenceDataGroups;
    }
    
    /**
     * @return IProcedure[]
     */
    public function GetExecutedProcedures() {
        return $this->ProcedureToExecute;
    }
    
    /**
     * @return array[]
     */
    public function GetDiscardenceData() {
        return $this->DiscardenceData;
    }
    
    /**
     * @return array[][]
     */
    public function GetDiscardenceDataGroups() {
        return $this->DiscardedIdentityGroups;
    }
    
    /**
     * @return ICriterion[]
     */
    public function GetDiscardedCriteria() {
        return $this->DiscardedCriteria;
    }
}

?>