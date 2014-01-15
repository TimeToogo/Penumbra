<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Core\Containers\Registrar;
use \Storm\Core\Containers\Map;

final class RequestMapper extends ObjectRelationalMapperBase {
    /**
     * @var CriteriaMapper 
     */
    private $CriteriaMapper;
    
    public function __construct(DomainDatabaseMap $DomainDatabaseMap, CriteriaMapper $CriteriaMapper) {
        parent::__construct($DomainDatabaseMap);
        
        $this->CriteriaMapper = $CriteriaMapper;
    }
    
    /**
     * @param Object\IRequest $ObjectRequest
     * @return Relational\Request
     */
    final public function MapRequest(Object\IRequest $ObjectRequest) {
        $EntityRelationalMap = $this->VerifyRelationalMap($ObjectRequest->GetEntityType());
        
        $RelationalRequest = new Relational\Request(array(), $EntityRelationalMap->GetCriterion());
        $this->MapPropetiesToRelationalRequest($EntityRelationalMap, $RelationalRequest, $ObjectRequest->GetProperties());
        
        $this->CriteriaMapper->MapCriterion($EntityRelationalMap, $ObjectRequest->GetCriterion(), $RelationalRequest->GetCriterion());
        
        return $RelationalRequest;
    }
    
    /**
     * @internal
     */
    final public function MapEntityToRelationalRequest($EntityType, Relational\Request $RelationalRequest) {
        $this->MapPropetiesToRelationalRequest($this->VerifyRelationalMap($EntityType), $RelationalRequest);
    }
    
    private function MapPropetiesToRelationalRequest(IEntityRelationalMap $EntityRelationalMap, Relational\Request $RelationalRequest, array $Properties = null) {
        if($Properties === null) {
            $Properties = $EntityRelationalMap->GetEntityMap()->GetProperties();
        }
        
        $DataPropertyColumnMappings = $EntityRelationalMap->GetDataPropertyColumnMappings();
        $EntityPropertyToOneRelationMappings = $EntityRelationalMap->GetEntityPropertyToOneRelationMappings();
        $CollectionPropertyToManyRelationMappings = $EntityRelationalMap->GetCollectionPropertyToManyRelationMappings();
        
        foreach($Properties as $PropertyIdentifier => $Property) {
            if(isset($DataPropertyColumnMappings[$PropertyIdentifier])) {
                $RelationalRequest->AddColumns($DataPropertyColumnMappings[$PropertyIdentifier]->GetReviveColumns());
            }
            else if(isset($EntityPropertyToOneRelationMappings[$PropertyIdentifier])) {
                $EntityPropertyToOneRelationMappings[$PropertyIdentifier]->AddToRelationalRequest($this, $RelationalRequest);
            }
            else if(isset($CollectionPropertyToManyRelationMappings[$PropertyIdentifier])) {
                $CollectionPropertyToManyRelationMappings[$PropertyIdentifier]->AddToRelationalRequest($this, $RelationalRequest);
            }
        }
    }
    
}

?>