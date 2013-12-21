<?php

namespace Storm\Drivers\Base\Relational\Columns\Traits;

use \Storm\Drivers\Base\Relational\Columns\ColumnTrait;

class Increment extends ColumnTrait {
    final protected function IsTrait(ColumnTrait $OtherTrait) {
        return true;
    }
}

?>