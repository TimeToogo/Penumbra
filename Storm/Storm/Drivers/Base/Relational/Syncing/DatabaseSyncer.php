<?php

namespace Storm\Drivers\Base\Relational\Syncing;

use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\Database;

abstract class DatabaseSyncer implements IDatabaseSyncer {
    private $Builder;
    private $Modifier;
    
    function __construct(IDatabaseBuilder $Builder, IDatabaseModifier $Modifier) {
        $this->Builder = $Builder;
        $this->Modifier = $Modifier;
    }

    
    final public function Sync(IConnection $Connection, Database $Database) {
        $this->SyncDatabase($Connection, $Database, $this->Builder, $this->Modifier);
    }
    protected abstract function SyncDatabase(IConnection $Connection, Database $Database, 
            IDatabaseBuilder $Builder, IDatabaseModifier $Modifier); 
}

?>