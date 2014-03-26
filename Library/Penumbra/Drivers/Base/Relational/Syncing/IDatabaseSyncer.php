<?php

namespace Penumbra\Drivers\Base\Relational\Syncing;

use \Penumbra\Drivers\Base\Relational;
use \Penumbra\Drivers\Base\Relational\Queries\IConnection;

interface IDatabaseSyncer {
    public function Sync(IConnection $Connection, Relational\Database $Database);
}

?>