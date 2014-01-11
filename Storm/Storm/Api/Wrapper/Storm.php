<?php

namespace Storm\Api\Wrapper;

use \Storm\Api\Base;

class Storm extends Base\Storm {
    private $WrappedStorm;
    
    public function __construct(Base\Storm $WrappedStorm) {
        $this->WrappedStorm = $WrappedStorm;
    }
    
    /**
     * @return Base\Storm
     */
    final protected function GetWrappedStorm() {
        return $this->WrappedStorm;
    }

    public function GetRepository($EntityType, $AutoSave = false) {
        return $this->WrappedStorm->GetRepository($EntityType, $AutoSave);
    }
    
    protected function ConstructRepository($EntityType, $AutoSave = false) {
        return $this->WrappedStorm->ConstructRepository($EntityType, $AutoSave);
    }
    
    public function GetDomainDatabaseMap() {
        return $this->WrappedStorm->GetDomainDatabaseMap();
    }
}

?>
