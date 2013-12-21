<?php

namespace Storm\Drivers\Base\Relational\Syncing;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Queries\IConnection;

interface IDatabaseBuilder {
    /**
     * @return Relational\Database
     */
    public function Build(IConnection $Connection);
}

?>