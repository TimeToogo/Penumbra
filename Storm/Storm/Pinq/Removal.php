<?php

namespace Storm\Pinq;

use \Storm\Core\Object\IEntityMap;
use \Storm\Api\IEntityManager;

class Removal extends Criteria  {
    /**
     * @var IEntityManager
     */
    private $EntityManager;
    
    public function __construct(
            IEntityManager $EntityManager,
            IFunctionToExpressionTreeConverter $FunctionToExpressionTreeConverter) {
        parent::__construct($EntityManager->GetEntityMap(), $FunctionToExpressionTreeConverter);
        
        $this->EntityManager = $EntityManager;
    }
    
    public function Execute() {
        $this->EntityManager->DiscardBy($this->BuildCriteria());
    }
}

?>