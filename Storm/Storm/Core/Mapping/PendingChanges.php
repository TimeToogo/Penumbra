<?php

namespace Storm\Core\Mapping;

use \Storm\Core\Object;
use \Storm\Core\Relational;
use \Storm\Core\Containers\Registrar;
use \Storm\Core\Containers\Map;

/**
 * TODO: Implement
 */
class PendingChanges {
    private $EntitiesToPersist = [];
    private $ProceduresToExecute = [];
    private $EntitiesToDiscard = [];
    private $CriteriaToDiscardBy = [];
    
    public function __construct() {
        
    }
    
    
}

?>