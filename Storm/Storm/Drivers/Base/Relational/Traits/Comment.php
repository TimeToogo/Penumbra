<?php

namespace Storm\Drivers\Base\Relational\Traits;

use \Storm\Drivers\Base\Relational\StructuralTableTrait;

class Comment extends StructuralTableTrait {
    private $Value;
    
    public function __construct($Value) {
        $this->Value = $Value;
    }
    
    final public function GetValue() {
        return $this->Value;
    }
    
    protected function IsStructuralTrait(StructuralTableTrait $OtherTrait) {
        return $OtherTrait->Value === $this->Value;
    }
}

?>