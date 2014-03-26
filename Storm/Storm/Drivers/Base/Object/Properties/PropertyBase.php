<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object;

abstract class PropertyBase implements Object\IProperty {
    private $Identifier;
    
    /**
     * @var string 
     */
    private $EntityType;
    
    public function __construct($Identifier) {
        $this->Identifier = $Identifier;
    }
    
    final public function GetIdentifier() {
        return $this->Identifier;
    }

    final public function GetEntityType() {
        return $this->EntityType;
    }
    
    final public function SetEntityType($EntityType) {
        if($this->EntityType === $EntityType) {
            return;
        }
        $this->EntityType = $EntityType;
        $this->OnSetEntityType($EntityType);
    }
    protected abstract function OnSetEntityType($EntityType);
}

?>
