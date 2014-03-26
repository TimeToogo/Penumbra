<?php

namespace Penumbra\Drivers\Platforms\SQLite\Queries;

use \Penumbra\Drivers\Platforms\Base\Queries;

final class IdentifierEscaper extends Queries\IdentifierEscaper {
    public function __construct() {
        parent::__construct('"', '"', '.', 'AS');
    }
}

?>