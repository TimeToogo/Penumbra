<?php

namespace Penumbra\Drivers\Platforms\Mysql\Columns;

use \Penumbra\Drivers\Base\Relational\Columns\ColumnTrait;

class Collation extends ColumnTrait {
    private $Name;
    public function __construct($Name) {
        $this->Name = $Name;
    }
    
    final public function GetName() {
        return $this->Name;
    }

    final protected function IsTrait(ColumnTrait $OtherTrait) {
        return $this->Name === $OtherTrait->Name;
    }
}

?>