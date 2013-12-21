<?php

namespace Storm\Drivers\Base\Object;

use \Storm\Core\Object\Identity;

abstract class EntityMap extends \Storm\Core\Object\EntityMap {
    private $EntityConstructor;
    
    public function __construct() {
        $this->EntityConstructor = $this->EntityConstructor();
        parent::__construct();
    }
    
    /**
     * @return Requests\EntityRequest
     */
    final public function Request($IsSingleEntity = false) {
        return new Requests\EntityRequest($this, $IsSingleEntity);
    }
    
    /**
     * @return Requests\IdEntityRequest
     */
    final public function RequestId(Identity $Identity) {
        return new Requests\IdEntityRequest($Identity);
    }
    
    protected abstract function EntityConstructor();
    
    final protected function ConstructEntity() {
        return $this->EntityConstructor->Construct($this->EntityType());
    }
}

?>
