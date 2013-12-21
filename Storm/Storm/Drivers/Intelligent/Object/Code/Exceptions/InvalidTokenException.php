<?php

namespace Storm\Drivers\Intelligent\Object\Code\Exceptions;

class InvalidTokenException extends \Exception {
    public function __construct($Message = 'The supplied token is invalid') {
        parent::__construct($Message);
    }
}

?>
