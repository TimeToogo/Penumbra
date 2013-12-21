<?php

namespace Storm\Drivers\Base\Object\Requests;

use Storm\Core\Object;

class IdEntityRequest extends EntityRequest {
    public function __construct(Object\Identity $Identity) {
        parent::__construct($Identity->GetEntityMap(), true);
        
        $Predicate = Object\Constraints\Predicate::On($this->GetEntityType());
        $Predicate->AddRules(Object\Constraints\RuleGroup::Matches($Identity));
        
        $this->AddPredicate($Predicate);
    }
}

?>