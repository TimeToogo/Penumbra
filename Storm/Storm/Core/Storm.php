<?php

namespace Storm\Core;

use \Storm\Core\Mapping\DomainDatabaseMap;

class Storm {
    private $ORM;
    
    final public function __construct(DomainDatabaseMap $ORM) {
        $this->ORM = $ORM;
    }
    
    /**
     * @return Repository
     */
    public function GetRepository($EntityType, $AutoSave = false) {
        if(is_object($EntityType))
            $EntityType = get_class($EntityType);
        return new Repository($this->ORM, $EntityType, $AutoSave);
    }
    
    /**
     * @return DomainDatabaseMap
     */
    public function GetORM() {
        return $this->ORM;
    }
}

?>
