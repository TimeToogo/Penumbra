<?php

namespace Penumbra\Drivers\Base\Relational\Columns\Traits;

use \Penumbra\Drivers\Base\Relational\Columns\ColumnTrait;

class NotNullable extends ColumnTrait {
    final protected function IsTrait(ColumnTrait $OtherTrait) {
        return true;
    }
}

?>