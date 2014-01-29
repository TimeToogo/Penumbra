<?php

namespace Storm\Drivers\Base\Object;

use \Storm\Core\Object\Identity;

abstract class EntityMap extends \Storm\Core\Object\EntityMap {
    private $EntityConstructor;
    
    public function __construct() {
        parent::__construct();
        $this->EntityConstructor = $this->EntityConstructor();
        if($this->EntityConstructor->HasEntityType()) {
            throw new Exception;
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
