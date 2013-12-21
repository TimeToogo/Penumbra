<?php

namespace Storm\Drivers\Base\Relational\Requests;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Constraints\Predicate;

class PrimaryKeyRequest extends Relational\Request {
    public function __construct(Relational\PrimaryKey $PrimaryKey) {
        parent::__construct($PrimaryKey->GetTable(), true);
        
        $Predicate = Predicate::On($PrimaryKey->GetTable())
                ->Matches($PrimaryKey);
        $this->AddPredicate($Predicate);
    }
}

?>