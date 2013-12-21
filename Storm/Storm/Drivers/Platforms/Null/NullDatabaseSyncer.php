<?php

namespace Storm\Drivers\Platforms\Null;

use \Storm\Drivers\Base\Relational;

final class NullDatabaseSyncer implements Relational\Syncing\IDatabaseSyncer {
    public function Sync(Relational\Queries\IConnection $Connection, Relational\Database $Database) { }
}

?>