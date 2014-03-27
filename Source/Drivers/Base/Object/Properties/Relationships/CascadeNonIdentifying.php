<?php

namespace Penumbra\Drivers\Base\Object\Properties\Relationships;

class CascadeNonIdentifying extends NonIdentifying {
    public function __construct() {
        parent::__construct(true, true);
    }
}


?>