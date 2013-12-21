<?php

namespace Storm\Drivers\Base\Mapping\Collections;

interface ICollection {
    public function GetEntityType();
    public function __IsAltered();
    public function __GetOriginalEntities();
    public function __GetRemovedEntities();
    public function ToArray();
}

?>
