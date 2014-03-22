<?php

namespace Storm\Drivers\Base\Mapping;

use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;

abstract class DomainDatabaseMap extends Mapping\DomainDatabaseMap {
    private $Platform;
    
    public function __construct(IPlatform $Platform) {
        $this->Platform = $Platform;
        parent::__construct();
    }
    
    final protected function OnInitialize(Object\Domain $Domain, Relational\Database $Database) {
        $Database->SetPlatform($this->Platform->GetRelationalPlatform());
    }
    
    final protected function MapToExistsSelect(Object\IRequest $Request) {
        $EntityRelationalMap = $this->VerifyEntityTypeIsMapped($Request->GetEntityType());
        
        return $this->Platform->MapToExistsSelect($Request, $EntityRelationalMap);
    }
    
    final protected function MapEntityRequest(Object\IEntityRequest $EntityRequest) {
        $EntityRelationalMap = $this->VerifyEntityTypeIsMapped($EntityRequest->GetEntityType());
        
        return $this->Platform->MapEntityRequest($EntityRequest, $EntityRelationalMap);
    }
    
    protected function MapDataRequest(Object\IDataRequest $DataRequest, array &$AliasReviveFuncionMap) {
        $EntityRelationalMap = $this->VerifyEntityTypeIsMapped($DataRequest->GetEntityType());
        
        return $this->Platform->MapDataRequest($DataRequest, $AliasReviveFuncionMap, $EntityRelationalMap);
    }
    
    final protected function MapProcedure(Object\IProcedure $Procedure) {
        $EntityRelationalMap = $this->VerifyEntityTypeIsMapped($Procedure->GetEntityType());
        
        return $this->Platform->MapProcedure($Procedure, $EntityRelationalMap);
    }
    
    final protected function MapCriteriaToDelete(Object\ICriteria $Criteria) {
        $EntityRelationalMap = $this->VerifyEntityTypeIsMapped($Criteria->GetEntityType());
        
        return $this->Platform->MapCriteriaToDelete($Criteria, $EntityRelationalMap);
    }    
}

?>