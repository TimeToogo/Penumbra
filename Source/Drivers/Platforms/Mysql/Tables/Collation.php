<?php

namespace Penumbra\Drivers\Platforms\Mysql\Tables;

use \Penumbra\Drivers\Base\Relational\StructuralTableTrait;

class Collation extends StructuralTableTrait {
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