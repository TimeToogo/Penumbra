<?php

namespace Storm\Core\Relational;

/**
 * This select represents a boolean of whether any data exists
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ExistsSelect extends Select {
    
    public function __construct(ResultSetSpecification $ResultSetSpecification) {
        parent::__construct($ResultSetSpecification);
    }

    final public function GetSelectType() {
        return SelectType::Exists;
    }
}

?>