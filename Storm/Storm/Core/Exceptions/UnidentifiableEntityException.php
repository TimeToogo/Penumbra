<?php

namespace Storm\Core\Exceptions;

class UnidentifiableEntityException extends \Exception {
    public function __construct() {
        parent::__construct('The supplied entity does not posses a valid identity');
    }
}

?>
