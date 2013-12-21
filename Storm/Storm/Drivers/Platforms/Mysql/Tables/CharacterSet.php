<?php

namespace Storm\Drivers\Platforms\Mysql\Tables;

use \Storm\Drivers\Base\Relational\StructuralTableTrait;

class CharacterSet extends StructuralTableTrait {
    private $Name;
    
    public function __construct($Name) {
        $this->Name = $Name;
    }
    
    final public function GetName() {
        return $this->Name;
    }
    
    protected function IsStructuralTrait(StructuralTableTrait $OtherTrait) {
        return $OtherTrait->Name === $this->Name;
    }
}

?>