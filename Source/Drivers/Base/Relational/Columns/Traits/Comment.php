<?php

namespace Penumbra\Drivers\Base\Relational\Columns\Traits;

use \Penumbra\Drivers\Base\Relational\Columns\ColumnTrait;

class Comment extends ColumnTrait {
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