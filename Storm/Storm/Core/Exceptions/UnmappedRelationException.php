<?php

namespace Storm\Core\Exceptions;

use \Storm\Core\Object\IProperty;

class UnmappedRelationException extends \Exception {
    public function __construct() {
        parent::__construct('The supplied relation is not mapped');
    }
}

?>
