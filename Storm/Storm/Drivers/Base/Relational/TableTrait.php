<?php

namespace Storm\Drivers\Base\Relational;

abstract class TableTrait {
    use \Storm\Core\Helpers\Type;
    
    final public function Is(TableTrait $OtherTrait) {
        if(!($OtherTrait instanceof static))
            return false;
        else
            return $this->IsTrait($OtherTrait);
    }
    protected abstract function IsTrait(TableTrait $OtherTrait);
}

?>