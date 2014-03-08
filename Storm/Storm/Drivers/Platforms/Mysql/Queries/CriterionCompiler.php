<?php

namespace Storm\Drivers\Platforms\Mysql\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Platforms\Base\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;

class CriterionCompiler extends Queries\CriterionCompiler {
    
    protected function JoinTypes() {
        $JoinTypes = parent::JoinTypes();
        unset($JoinTypes[Relational\JoinType::Full]);
        return $JoinTypes;
    }
    
}

?>