<?php

namespace Penumbra\Drivers\Base\Relational\Traits;

use \Penumbra\Drivers\Base\Relational\StructuralTableTrait;

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