<?php

namespace Storm\Drivers\Base\Object\Properties;

class HiddenGetterField extends GetterField {
    public function __construct($Name) {
        parent::__construct($Name, false);
    }
}

?>
