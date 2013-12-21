<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

interface IToOneRelation extends Relational\IToOneRelation {
    /**
     * @return ForeignKey
     */
    public function GetForeignKey();
}

?>