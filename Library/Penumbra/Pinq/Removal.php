<?php

namespace Penumbra\Pinq;

use \Penumbra\Core\Object\IEntityMap;
use \Penumbra\Api\IEntityManager;

class Removal extends Criteria  {
    /**
     * @var IEntityManager
     */
    private $EntityManager;
    
    public function __construct(
            IEntityManager $EntityManager,
            Functional\IFunctionToExpressionTreeConverter $FunctionToExpressionTreeConverter) {
        parent::__construct($EntityManager->GetEntityMap(), $FunctionToExpressionTreeConverter);
        
        $this->EntityManager = $EntityManager;
    }
    
    public function Execute() {
        $this->EntityManager->DiscardBy($this->BuildCriteria());
    }
}

?>