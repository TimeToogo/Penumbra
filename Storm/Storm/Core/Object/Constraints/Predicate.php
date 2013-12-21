<?php

namespace Storm\Core\Object\Constraints;

use \Storm\Core\Object\IProperty;

class Predicate implements IPredicate {
    private $EntityType;
    private $RuleGroups = array();
    
    protected function __construct($EntityOrType) {
        if(!is_string($EntityOrType))
            $EntityOrType = get_class($EntityOrType);
        
        $this->EntityType = $EntityOrType;
    }
    
    public function GetEntityType() {
        return $this->EntityType;
    }
        
    public static function On($EntityOrType) {
        return new Predicate($EntityOrType);
    }
    
    public function AddRules(RuleGroup $Rules) {
        $this->RuleGroups[] = $Rules;
    }
    
    public function GetRuleGroups() {
        return $this->RuleGroups;
    }
}

?>