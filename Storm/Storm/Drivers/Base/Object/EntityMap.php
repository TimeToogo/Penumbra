<?php

namespace Storm\Drivers\Base\Object;

use Storm\Core\Object;

abstract class EntityMap extends Object\EntityMap {
    private $EntityConstructor;
    
    public function __construct() {
        parent::__construct();
        $this->EntityConstructor = $this->EntityConstructor();
        if($this->EntityConstructor->HasEntityType()) {
            throw new Object\ObjectException(
                    'The supplied entity constructor %s already has an entity type %s',
                    get_class($this->EntityConstructor),
                    $this->EntityConstructor->GetEntityType());
        }
        $this->EntityConstructor->SetEntityType($this->GetEntityType());
    }
    
    /**
     * @return Construction\IEntityConstructor
     */
    protected abstract function EntityConstructor();
    
    final protected function ConstructEntity() {
        return $this->EntityConstructor->Construct();
    }
}

?>
