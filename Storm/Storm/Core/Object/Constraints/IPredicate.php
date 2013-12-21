<?php

namespace Storm\Core\Object\Constraints;

interface IPredicate {
    public function GetEntityType();
    
    public function AddRules(RuleGroup $Rules);
    
    /**
     *@return RuleGroup[]
     */
    public function GetRuleGroups();
}

?>