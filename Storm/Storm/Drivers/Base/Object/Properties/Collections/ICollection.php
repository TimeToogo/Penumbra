<?php

namespace Storm\Drivers\Base\Object\Properties\Collections;

interface ICollection {
    public function GetEntityType();
    public function __IsAltered();
    public function ToArray();
}

?>