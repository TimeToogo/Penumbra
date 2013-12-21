<?php

namespace Storm\Drivers\Platforms\Null;

use \Storm\Drivers\Base\Relational;

final class NullToOneReviver implements Relational\Relations\IToOneReviver {
    public function Revive(Relational\Queries\IConnection $Connection, 
            \Storm\Core\Relational\IToOneRelation $ToManyRelation, array $Rows) { }
}

?>