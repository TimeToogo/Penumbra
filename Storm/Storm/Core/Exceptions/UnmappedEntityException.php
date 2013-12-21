<?php

namespace Storm\Core\Exceptions;

class UnmappedEntityException extends \Exception {
    public function __construct($EntityType) {
        parent::__construct('There is no registered EntityMap for the type ' . $EntityType);
    }
}

?>
