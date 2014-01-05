<?php

namespace Storm\Drivers\Base\Object\Construction;

interface IEntityConstructor {
    public function SetEntityType($EntityType);
    public function HasEntityType();
    public function GetEntityType();
    
    public function Construct();
}

?>
