<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Drivers\Base\Relational\Queries\IConnection;
use Storm\Core\Relational\IToManyRelation;

interface IToManyReviver {
    public function Revive(IConnection $Connection, 
            IToManyRelation $ToManyRelation, array $Rows);
}

?>