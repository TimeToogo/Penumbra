<?php

namespace Storm\Drivers\Base\Relational\Columns\Traits;

use \Storm\Drivers\Base\Relational\Columns\ColumnTrait;

class DefaultValue extends ColumnTrait {
    private $Value;
    public function __construct($Value) {
        $this->Value = $Value;
    }
    
    final public function GetValue() {
        return $this->Value;
    }

    final protected function IsTrait(ColumnTrait $OtherTrait) {
        return $this->Value === $OtherTrait->Value;
    }
}

?>