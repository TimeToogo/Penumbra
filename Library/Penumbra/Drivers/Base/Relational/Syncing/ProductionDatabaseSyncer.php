<?php

namespace Penumbra\Drivers\Base\Relational\Syncing;

use \Penumbra\Drivers\Base\Relational\Queries\IConnection;
use \Penumbra\Core\Relational\Database;

final class ProductionDatabaseSyncer implements IDatabaseSyncer {
    public function Sync(IConnection $Connection, Database $Database) { }
}

?>