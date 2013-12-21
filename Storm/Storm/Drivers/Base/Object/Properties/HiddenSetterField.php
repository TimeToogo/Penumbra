<?php

namespace Storm\Drivers\Base\Object\Properties;

class HiddenSetterField extends SetterField {
    public function __construct($Name) {
        parent::__construct($Name, false);
    }
}

?>
