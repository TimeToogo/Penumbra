<?php

namespace Storm\Core\Relational;

/**
 * This select represents a boolean of whether any data exists
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ExistsSelect extends Select {
    
    public function __construct( Criterion $Criterion) {
        parent::__construct($Criterion);
    }

    final public function GetSelectType() {
        return SelectType::Exists;
    }
}

?>