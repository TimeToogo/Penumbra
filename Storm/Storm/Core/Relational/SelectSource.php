<?php

namespace Storm\Core\Relational;

class SelectSource {
    /**
     * @var ITable|Select
     */
    private $Source;
    
    /**
     * @var Join[] 
     */
    private $Joins = [];
        
    /**
     * @return ITable[]
     */
    final public function GetTables() {
        return $this->Criteria->GetAllTables();
    }
}

?>