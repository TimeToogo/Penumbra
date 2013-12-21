<?php

namespace Storm\Drivers\Platforms\Null;

use \Storm\Drivers\Base\Relational;

final class NullToManyReviver implements Relational\Relations\IToManyReviver {
    public function Revive(Relational\Queries\IConnection $Connection, 
            \Storm\Core\Relational\IToManyRelation $ToManyRelation, array $Rows) { }
}

?>