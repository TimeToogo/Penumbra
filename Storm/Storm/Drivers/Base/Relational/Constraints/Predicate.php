<?php

namespace Storm\Drivers\Base\Relational\Constraints;

use \Storm\Core\Relational;
use \Storm\Core\Relational\TableColumnData;

class Predicate extends Relational\Constraints\Predicate {
    
    public function Matches(TableColumnData $ColumnData) {
        $this->AddRules(RuleGroup::Matches($ColumnData));
        
        return $this;
    }
}

?>