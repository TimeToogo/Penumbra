<?php

namespace Storm\Api\Wrapper;

use \Storm\Api\Base;
use \Storm\Core\Object;

class Repository extends Base\Repository {
    private $WrappedRepository;
    
    final public function __construct(Base\Repository $WrappedRepository) {
        $this->WrappedRepository = $WrappedRepository;
    }
    
    /**
     * @return Base\Repository
     */
    final protected function GetWrappedRepository() {
        return $this->WrappedRepository;
    }

    public function Discard($EntityOrCriterion) {
        return $this->WrappedRepository->Discard($EntityOrCriterion);
    }

    public function DiscardAll(array $Entities) {
        return $this->WrappedRepository->DiscardAll($Entities);
    }

    public function Execute(Base\Fluent\ProcedureBuilder $ProcedureBuilder) {
        return $this->WrappedRepository->Execute($ProcedureBuilder);
    }

    public function Load(Base\Fluent\RequestBuilder $RequestBuilder) {
        return $this->WrappedRepository->Load($RequestBuilder);
    }

    public function Persist($Entity) {
        return $this->WrappedRepository->Persist($Entity);
    }

    public function PersistAll(array $Entities) {
        return $this->WrappedRepository->PersistAll($Entities);
    }

    public function SaveChanges() {
        return $this->WrappedRepository->SaveChanges();
    }
}

?>