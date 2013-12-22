<?php

namespace Storm\Core\Relational\Constraints;

use \Storm\Core\Relational\Table;
use \Storm\Core\Relational\ColumnData;

class Predicate {
    private $RuleGroups = array();
    
    private function VerifyRules(RuleGroup $Rules) {
        foreach($Rules->GetRules() as $Rule) {
            if(!($Rule instanceof Rule))
                throw new \InvalidArgumentException('$Rules must only contain instance of Rule');
        }
    }
    
    public function AddRules(RuleGroup $Rules) {
        $this->VerifyRules($Rules);
        
        $this->RuleGroups[] = $Rules;
        
        return $this;
    }
    
    /**
     * @return RuleGroup
     */
    public function GetRuleGroups() {
        return $this->RuleGroups;
    }
}

?>