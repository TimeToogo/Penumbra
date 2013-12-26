<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

abstract class Accessor {
    private $Identifier;
    private $EntityType = null;
    
    final public function GetIdentifier() {
        if($this->Identifier === null) {
            $this->UpdateIdentifer();
        }
        return $this->Identifier;
    }
    private function UpdateIdentifer() {
        $Identifer = '';
        $this->Identifier($Identifer);
        $this->Identifier = md5(($this->EntityType ? $this->EntityType : '') . $Identifer);
    }
    protected abstract function Identifier(&$Identifier);
        
    public function SetEntityType($EntityType) {
        if($EntityType === $this->EntityType) {
            return;
        }
        $this->EntityType = $EntityType;
        $this->UpdateIdentifer();        
    }
    
    public abstract function GetValue($Entity);
    public abstract function SetValue($Entity, $PropertyValue);
}

?>
