<?php

namespace Storm\Drivers\Intelligent\Object\Pinq;

use \Storm\Drivers\Base\Object;
use \Storm\Drivers\Constant\Object\EntityMap;

class Request extends Object\Request {    
    public function __construct(EntityMap $EntityMap, array $Properties = null, $IsSingleEntity = false) {
        parent::__construct(
                $EntityMap->GetEntityType(), 
                $Properties ?: $EntityMap->GetProperties(),
                $IsSingleEntity,
                new Criterion($EntityMap));
    }
    
    /**
     * @return Request
     */
    public static function Entity(EntityMap $EntityMap, array $Properties = null, $IsSingleEntity = false) {
        return new Request($EntityMap, $Properties, $IsSingleEntity);
    }
    
    /**
     * @return Criterion
     */
    public function GetCriterion() {
        return parent::GetCriterion();
    }
}

?>
