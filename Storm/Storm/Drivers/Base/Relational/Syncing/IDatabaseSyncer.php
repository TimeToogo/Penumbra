<?php

namespace Storm\Drivers\Base\Relational\Syncing;

use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\Queries\IConnection;

interface IDatabaseSyncer {
    public function Sync(IConnection $Connection, Relational\Database $Database);
}

?>