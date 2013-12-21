<?php

namespace Storm\Core\Object\Constraints;

use \Storm\Core\Object\PropertyData;
use \Storm\Core\Object\Expressions\Expression;
use \Storm\Core\Object\Expressions\Operators;

class RuleGroup implements \IteratorAggregate {
    private $Rules = array();
    private $RuleGroups = array();
    private $IsAllRequired;
    
    protected function __construct(array $Rules, array $RuleGroups, $IsAllRequired) {
        $this->Rules = $Rules;
        $this->RuleGroups = $RuleGroups;
        $this->IsAllRequired = $IsAllRequired;
    }
    
    final public static function All(array $Rules = array(), array $RuleGroups = array()) {
        return new RuleGroup($Rules, $RuleGroups, true);
    }
    
    final public static function Any(array $Rules = array(), array $RuleGroups = array()) {
        return new RuleGroup($Rules, $RuleGroups, false);
    }
    
    final public static function Matches(PropertyData $PropertyData) {
        $Rules = array();
        $EntityMap = $PropertyData->GetEntityMap();
        foreach($PropertyData as $PropertyName => $Value) {
            $Rules[] = new Rule(
                    Expression::BinaryOperation(
                            Expression::Property($EntityMap->GetProperty($PropertyName)), 
                            Operators\Binary::Identity, 
                            Expression::Constant($Value)));
        }
        
        return RuleGroup::All($Rules);
    }
    
    final public function AddRule(IRule $Rule) {
        $this->Rules[] = $Rule;
    }
    
    final public function AddRuleGroup(self $RuleGroup) {
        $this->RuleGroups[] = $RuleGroup;
    }
    
    final public function IsAllRequired() {
        return $this->IsAllRequired;
    }
    
    /**
     * @return IRule[]
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

    final public function getIterator() {
        return new \ArrayIterator($this->Rules);
    }
}

?>