<?php

namespace Storm\Drivers\Base\Relational\Columns;

use \Storm\Drivers\Base\Relational\Queries;

abstract class ColumnTrait {
    use \Storm\Core\Helpers\Type;
    
    public function AllowMultiple() {
        return false;
    }
    
    final public function Is(ColumnTrait $OtherTrait) {
        if(!($OtherTrait instanceof static))
            return false;
        else
            return $this->IsTrait($OtherTrait);
    }
    protected abstract function IsTrait(ColumnTrait $OtherTrait);
}

?>
