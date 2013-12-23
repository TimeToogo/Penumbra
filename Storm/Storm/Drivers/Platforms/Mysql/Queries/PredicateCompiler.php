<?php

namespace Storm\Drivers\Platforms\Mysql\Queries;

use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Core\Relational\Constraints;

final class PredicateCompiler extends Queries\PredicateCompiler {
    protected function AppendRuleGroups(QueryBuilder $QueryBuilder, array $RuleGroups) {
        $First = true;
        foreach($RuleGroups as $RuleGroup) {
            if($First) $First = false;
            else 
                $QueryBuilder->Append(' AND ');
            
            $QueryBuilder->Append('(');
            $this->AppendRuleGroup($QueryBuilder, $RuleGroup);
            $QueryBuilder->Append(')');
        }
    }

    protected function AppendAndRules(QueryBuilder $QueryBuilder, array $RuleGroups, array $Rules) {
        $this->AppendRules($QueryBuilder, $RuleGroups, $Rules, 'AND');
    }

    protected function AppendOrRules(QueryBuilder $QueryBuilder, array $RuleGroups, array $Rules) {
        $this->AppendRules($QueryBuilder, $RuleGroups, $Rules, 'OR');
    }
    
    private function AppendRules(QueryBuilder $QueryBuilder, array $RuleGroups, array $Rules, $Seperator) {
        $First = true;
        foreach(array_merge($RuleGroups, $Rules) as $Rule) {
            if($First) $First = false;
            else 
                $QueryBuilder->Append(' ' . $Seperator . ' ');
            
            $QueryBuilder->Append('(');
            if($Rule instanceof Constraints\Rule) {
                $this->AppendRule($QueryBuilder, $Rule);
            }
            else if($Rule instanceof Constraints\RuleGroup) {
                $this->AppendRuleGroup($QueryBuilder, $Rule);
            }
            $QueryBuilder->Append(')');
        }
    }
}

?>