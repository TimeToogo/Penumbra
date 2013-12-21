<?php

namespace Storm\Core\Relational\Constraints;

use \Storm\Core\Relational\ColumnData;

class RuleGroup {
    private $Rules = array();
    private $RuleGroups = array();
    private $IsAllRequired;
    
    public function __construct(array $Rules, $IsAllRequired) {
        $this->Rules = $Rules;
        $this->IsAllRequired = $IsAllRequired;
    }
    
    final public static function All(array $Rules = array()) {
        return new static($Rules, true);
    }
    
    final public static function Any(array $Rules = array()) {
        return new static($Rules, false);
    }
    
    final public function AddRule(Rule $Rule) {
        $this->Rules[] = $Rule;
    }
    
    final public function AddRuleGroup(self $RuleGroup) {
        $this->RuleGroups[] = $RuleGroup;
    }
    
    final public function IsAllRequired() {
        return $this->IsAllRequired;
    }
    
    /**
     * @return Rule[]
     */
    final public function GetRules() {
        return $this->Rules;
    }
    
    /**
     * @return RuleGroup[]
     */
    final public function GetRuleGroups() {
        return $this->RuleGroups;
    }
}

?>