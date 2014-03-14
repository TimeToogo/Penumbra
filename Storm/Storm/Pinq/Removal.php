<?php

namespace Storm\Pinq;

use \Storm\Core\Object\IEntityMap;
use \Storm\Api\Base\Repository;

class Removal extends Criteria  {
    /**
     * @var Repository
     */
    private $Repository;
    
    public function __construct(
            Repository $Repository,
            IEntityMap $EntityMap, 
            IFunctionToExpressionTreeConverter $FunctionToExpressionTreeConverter) {
        parent::__construct($EntityMap, $FunctionToExpressionTreeConverter);
        
        $this->Repository = $Repository;
    }
    
    public function Execute() {
        $this->Repository->Discard($this->BuildCriteria());
    }
}

?>