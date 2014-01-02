<?php

namespace Storm\Drivers\Base\Relational\Requests;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Constraints\Predicate;
use \Storm\Drivers\Base\Relational\Constraints\RuleGroup;

class PrimaryKeyRequest extends Relational\Request {
    public function __construct(array $PrimaryKeys) {
        if(count($PrimaryKeys) === 0) {
            throw new \Exception;//TODO: error message
        }
        parent::__construct(reset($PrimaryKeys)->GetTable()->GetColumns(), true);
        
        $Predicate = new Predicate();
        $RuleGroup = RuleGroup::Any();
        foreach($PrimaryKeys as $PrimaryKey) {
            $RuleGroup->AddRuleGroup(RuleGroup::Matches($PrimaryKey));
        }
        $Predicate->AddRules($RuleGroup);
        $this->AddPredicate($Predicate);
    }
}

?>