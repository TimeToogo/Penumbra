<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Core\Containers\Registrar;
use \Storm\Core\Containers\Map;

final class ProcedureMapper extends ObjectRelationalMapperBase {
    /**
     * @var CriteriaMapper 
     */
    private $CriteriaMapper;
    
    public function __construct(DomainDatabaseMap $DomainDatabaseMap, CriteriaMapper $CriteriaMapper) {
        parent::__construct($DomainDatabaseMap);
        
        $this->CriteriaMapper = $CriteriaMapper;
    }
    
    /**
     * @param Object\IRequest $ObjectProcedure
     * @return Relational\Procedure
     */
    public function MapProcedure(Object\IProcedure $ObjectProcedure) {
        $EntityRelationalMap = $this->DomainDatabaseMap->GetRelationMap($ObjectProcedure->GetEntityType());
        
        $RelationalProcedure = new Relational\Procedure(
                $EntityRelationalMap->GetMappedPersistTables(), $EntityRelationalMap->GetCriterion());
        
        $this->CriteriaMapper->MapCriterion($EntityRelationalMap, $ObjectProcedure->GetCriterion(), $RelationalProcedure->GetCriterion());
        $RelationalExpressions = $this->CriteriaMapper->GetExpressionMapper->MapExpressions($EntityRelationalMap, $ObjectProcedure->GetExpressions());
        
        foreach($RelationalExpressions as $RelationalExpression) {
            $RelationalProcedure->AddExpression($RelationalExpression);
        }
        
        return $RelationalProcedure;
    }
    
}

?>