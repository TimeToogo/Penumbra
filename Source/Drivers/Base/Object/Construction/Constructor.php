<?php

namespace Penumbra\Drivers\Base\Object\Construction;

 abstract class Constructor implements IConstructor {
    protected $EntityType;
    
    final public function GetEntityType() {
        return $this->EntityType;
    }

    public function HasEntityType() {
        return $this->EntityType !== null;
    }

    public function SetEntityType($EntityType) {
        if($this->EntityType === $EntityType) {
            return $this->EntityType;
        }
        $this->EntityType = $EntityType;
        $this->OnSetEntityType($EntityType);
    }
    protected abstract function OnSetEntityType($EntityType);
}

?>
