<?php

namespace Storm\Drivers\Intelligent\Object\Pinq;

use \Storm\Drivers\Base\Object\Requests;
use \Storm\Drivers\Constant\Object\EntityMap;

class Request extends Requests\Request {
    private $EntityMap;
    
    public function __construct(EntityMap $EntityMap, $IsSingleEntity = false) {
        parent::__construct($EntityMap->GetEntityType(), $IsSingleEntity);
        
        $this->EntityMap = $EntityMap;
    }
    
    public function Where(\Closure $Predicate) {
        $this->AddPredicate(new Predicate($this->EntityMap, $Predicate));
        
        return $this;
    }
    
    public function OrderBy($param) {
        $this->AddOrderByProperty($param, true);
        
        return $this;
    }
    
    public function OrderByDescending($param) {
        $this->AddOrderByProperty($param, false);
        
        return $this;
    }
    
    public function Skip($Amount) {
        $this->SetRangeOffset($Amount);
        
        return $this;
    }
    
    public function Limit($Amount) {
        $this->SetRangeOffset($Amount);
        
        return $this;
    }
}

?>