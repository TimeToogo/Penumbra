<?php

namespace Penumbra\Drivers\Base\Mapping\Queries;

use \Penumbra\Core\Mapping;
use \Penumbra\Core\Object;
use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Mapping\ExpressionMapper;

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