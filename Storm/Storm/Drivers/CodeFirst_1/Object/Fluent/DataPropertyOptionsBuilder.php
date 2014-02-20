<?php

namespace Storm\Drivers\CodeFirst\Object\Fluent;

use \Storm\Drivers\Base\Object\Properties;
use \Storm\Drivers\CodeFirst\Object\Metadata;

class DataPropertyOptionsBuilder extends PropertyOptionsBuilder {
    public function Named($Name) {
        $this->Metadata->Add(new Metadata\Name($Name));
    }
}