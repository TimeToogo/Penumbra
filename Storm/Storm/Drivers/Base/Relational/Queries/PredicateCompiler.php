<?php

namespace Storm\Drivers\Base\Relational\Queries;

use \Storm\Core\Relational\Constraints\Predicate;
use \Storm\Core\Relational\Constraints\RuleGroup;
use \Storm\Core\Relational\Constraints\Rule;

abstract class PredicateCompiler implements IPredicateCompiler {
    final public function Append(QueryBuilder $QueryBuilder, Predicate $Predicate) {
        $this->AppendRuleGroups($QueryBuilder, $Predicate->GetRuleGroups());
    }
    
    protected abstract function AppendRuleGroups(QueryBuilder $QueryBuilder, array $RuleGroups);
    
    final protected function AppendRuleGroup(QueryBuilder $QueryBuilder, RuleGroup $RuleGroup) {
        if($RuleGroup->IsAllRequired()) {
            $this->AppendAndRules($QueryBuilder, $RuleGroup->GetRuleGroups(), $RuleGroup->GetRules());
        }
        else {
            $this->AppendOrRules($QueryBuilder, $RuleGroup->GetRuleGroups(), $RuleGroup->GetRules());
        }
    }
    
    protected abstract function AppendAndRules(QueryBuilder $QueryBuilder, array $RuleGroups, array $Rules);
    protected abstract function AppendOrRules(QueryBuilder $QueryBuilder, array $RuleGroups, array $Rules);
    
    final protected function AppendRule(QueryBuilder $QueryBuilder, Rule $Rule) {
        $QueryBuilder->AppendExpression($Rule->GetExpression());
    }
}

?>