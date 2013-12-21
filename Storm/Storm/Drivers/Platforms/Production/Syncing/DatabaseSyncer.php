<?php

namespace Storm\Drivers\Platforms\Production\Syncing;

use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\Database;

final class DatabaseSyncer implements \Storm\Drivers\Base\Relational\Syncing\IDatabaseSyncer {
    public function Sync(IConnection $Connection, Database $Database) { }
}

?>