<?php

namespace Storm\Drivers\Intelligent\Object\Pinq;

use \Storm\Drivers\Base\Object;
use \Storm\Core\Object\EntityMap;
use \Storm\Drivers\Intelligent\Object\Closure;

class Request extends Object\Request {    
    public function __construct(
            EntityMap $EntityMap, 
            array $Properties = null, 
            $IsSingleEntity = false,
            \Storm\Core\Object\ICriterion $Criterion = null) {
        
        parent::__construct(
                $EntityMap->GetEntityType(),
                $Properties ?: $EntityMap->GetProperties(),
                $IsSingleEntity,
                $Criterion ?: new Criterion($EntityMap));
    }
}

?>
