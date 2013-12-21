<?php

namespace Storm\Core\Exceptions;

use \Storm\Core\Object\IProperty;

class UnmappedPropertyException extends \Exception {
    public function __construct() {
        parent::__construct('The supplied property is not mapped to column');
    }
}

?>
