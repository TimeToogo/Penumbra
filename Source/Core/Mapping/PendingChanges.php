<?php

namespace Penumbra\Core\Mapping;

use \Penumbra\Core\Object;

class PendingChanges {
    /**
     * @var object[]
     */
    private $EntitiesToPersist;
    /**
     * @var Object\IProcedure[]
     */
    private $ProceduresToExecute;
    /**
     * @var object[]
     */
    private $EntitiesToDiscard;
    /**
     * @var Object\ICriteria[]
     */
    private $CriteriaToDiscardBy;
    
    
    public function __construct() {
        $this->Reset();
    }
    
    public function Reset() {
        $this->EntitiesToPersist = new \SplObjectStorage();
        $this->ProceduresToExecute = new \SplObjectStorage();
        $this->EntitiesToDiscard = new \SplObjectStorage();
        $this->CriteriaToDiscardBy = new \SplObjectStorage();
    }
    
    public function IsEmpty() {
        return !($this->EntitiesToPersist->count() > 0
                || $this->ProceduresToExecute->count() > 0
                || $this->EntitiesToDiscard->count() > 0
                || $this->CriteriaToDiscardBy->count() > 0);
    }
    
    /**
     * @return object[]
     */
    public function GetEntitiesToPersist() {
        return $this->EntitiesToPersist;
    }

    /**
     * @return Object\IProcedure[]
     */
    public function GetProceduresToExecute() {
        return $this->ProceduresToExecute;
    }

    /**
     * @return object[]
     */
    public function GetEntitiesToDiscard() {
        return $this->EntitiesToDiscard;
    }

    /**
     * @return Object\ICriteria[]
     */
    public function GetCriteriaToDiscardBy() {
        return $this->CriteriaToDiscardBy;
    }
    
    public function AddEntityToPersist($Entity) {
        $this->EntitiesToPersist->attach($Entity);
    }
    
    public function AddEntitiesToPersist(array $Entities) {
        foreach($Entities as $Entity) {
            $this->EntitiesToPersist->attach($Entity);
        }
    }

    public function AddProcedureToExecute(Object\IProcedure $Procedure) {
        $this->ProceduresToExecute->attach($Procedure);
    }
    
    public function AddProceduresToExecute(array $Procedures) {
        foreach($Procedures as $Procedure) {
            $this->ProceduresToExecute->attach($Procedure);
        }
    }

    public function AddEntityToDiscard($Entity) {
        $this->EntitiesToDiscard->attach($Entity);
    }
    
    public function AddEntitiesToDiscard(array $Entities) {
        foreach($Entities as $Entity) {
            $this->EntitiesToDiscard->attach($Entity);
        }
    }

    public function AddCriteriaToDiscardBy(Object\ICriteria $Criteria) {
        $this->CriteriaToDiscardBy->attach($Criteria);
    }
    
    public function AddAllCriteriaToDiscardBy(array $CriteriaArray) {
        foreach($CriteriaArray as $Criteria) {
            $this->CriteriaToDiscardBy->attach($Criteria);
        }
    }
    
    public function Merge(PendingChanges $PendingChanges) {
        if($PendingChanges->IsEmpty()) {
            return;
        }
        
        $this->EntitiesToPersist->addAll($PendingChanges->EntitiesToPersist);
        $this->ProceduresToExecute->addAll($PendingChanges->ProceduresToExecute);
        $this->EntitiesToDiscard->addAll($PendingChanges->EntitiesToDiscard);
        $this->CriteriaToDiscardBy->addAll($PendingChanges->CriteriaToDiscardBy);
    }
}

?>