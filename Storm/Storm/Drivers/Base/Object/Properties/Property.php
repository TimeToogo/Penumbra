<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object;
use \Storm\Core\Object\IProperty;
use \Storm\Core\Object\IEntityMap;

abstract class Property implements IProperty {
    private $Identifier;
    /**
     * @var Accessors\Accessor 
     */
    protected $Accessor;
    
    /**
     * @var string 
     */
    private $EntityType;
    
    public function __construct(Accessors\Accessor $Accessor) {
        $this->Identifier = $Accessor->GetIdentifier();
        $this->Accessor = $Accessor;
    }
    
    final public function GetIdentifier() {
        return $this->Identifier;
    }
    
    /**
     * @return Accessors\Accessor
     */
    final public function GetAccessor() {
        return $this->Accessor;
    }
    
    final public function GetEntityType() {
        return $this->EntityType;
    }
    
    final public function SetEntityType($EntityType) {
        $this->EntityType = $EntityType;
        $this->Accessor->SetEntityType($EntityType);
    }
}

?>
