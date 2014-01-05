<?php

namespace Storm\Drivers\Base\Object\Construction;

 abstract class Constructor implements IEntityConstructor {
    private $EntityType;
    
    final public function GetEntityType() {
        return $this->EntityType;
    }

    public function HasEntityType() {
        return $this->EntityType !== null;
    }

    public function SetEntityType($EntityType) {
        $this->EntityType = $EntityType;
        $this->OnSetEntityType($EntityType);
    }
    protected abstract function OnSetEntityType($EntityType);
}

?>
