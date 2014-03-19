<?php

namespace Storm\Drivers\Base\Mapping\Queries;

use \Storm\Core\Mapping;
use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Mapping\ExpressionMapper;

class DeleteMapper extends CriteriaMapper {
    
    public function MapCriteriaToDelete(
            Object\ICriteria $Criteria,
            Relational\Delete $Delete,
            Mapping\IEntityRelationalMap $EntityRelationalMap,
            ExpressionMapper $ExpressionMapper) {
        $Delete->AddTables($EntityRelationalMap->GetMappedPersistTables());
        $this->MapCriteria($Criteria, $Delete->GetCriteria(), $ExpressionMapper);
        
        return $Delete;
    }
}

?>