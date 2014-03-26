<?php

namespace Penumbra\Drivers\Base\Relational\Syncing;

use \Penumbra\Core\Relational;
use \Penumbra\Drivers\Base\Relational\Queries\IConnection;

interface IDatabaseBuilder {
    /**
     * @return Relational\Database
     */
    public function Build(IConnection $Connection);
}

?>