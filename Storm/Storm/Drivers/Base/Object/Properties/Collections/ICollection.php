<?php

namespace Storm\Drivers\Base\Object\Properties\Collections;

interface ICollection {
    public function GetEntityType();
    public function __IsAltered();
    public function __GetOriginalEntities();
    public function __GetRemovedEntities();
    public function ToArray();
}

?>
