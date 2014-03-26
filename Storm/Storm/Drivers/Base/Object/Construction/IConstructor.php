<?php

namespace Storm\Drivers\Base\Object\Construction;

use \Storm\Core\Object;

interface IConstructor {
    const IConstructorType = __CLASS__;
    
    public function SetEntityType($EntityType);
    public function HasEntityType();
    public function GetEntityType();
    
    public function Construct(Object\RevivalData $RevivalData);
}

?>
