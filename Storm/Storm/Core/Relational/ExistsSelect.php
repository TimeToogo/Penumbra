<?php

namespace Storm\Core\Relational;

/**
 * This select represents a boolean of whether any data exists
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ExistsSelect extends Select {
    
    public function __construct( Criteria $Criteria) {
        parent::__construct($Criteria);
    }

    final public function GetSelectType() {
        return SelectType::Exists;
    }
}

?>