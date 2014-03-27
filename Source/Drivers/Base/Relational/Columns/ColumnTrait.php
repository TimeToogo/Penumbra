<?php

namespace Penumbra\Drivers\Base\Relational\Columns;

use \Penumbra\Drivers\Base\Relational\Queries;

abstract class ColumnTrait {
    use \Penumbra\Core\Helpers\Type;
    
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
