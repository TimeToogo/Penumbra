<?php

namespace Storm\Drivers\Base\Relational\Syncing;

use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Core\Relational\Database;

final class ProductionDatabaseSyncer implements IDatabaseSyncer {
    public function Sync(IConnection $Connection, Database $Database) { }
}

?>