<?php

namespace Penumbra\Drivers\Platforms\Production\Syncing;

use \Penumbra\Drivers\Base\Relational\Queries\IConnection;
use \Penumbra\Drivers\Base\Relational\Database;

final class DatabaseSyncer implements \Penumbra\Drivers\Base\Relational\Syncing\IDatabaseSyncer {
    public function Sync(IConnection $Connection, Database $Database) { }
}

?>