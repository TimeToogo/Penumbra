<?php

namespace Storm\Drivers\Base\Object\Properties\Types;

use \Storm\Core\Object;

class CollectionPersistanceData {
    private $PersistedData;
    private $DiscardedIdentities;
    
    public function __construct(array $PersistedData, array $DiscardedIdentities) {
        $this->PersistedData = $PersistedData;
        $this->DiscardedIdentities = $DiscardedIdentities;
    }
    
    /**
     * @return Object\PersistenceData[]
     */
    public function GetPersistedData() {
        return $this->PersistedData;
    }

    /**
     * @return Object\Identity[]
     */
    public function GetDiscardedIdentities() {
        return $this->DiscardedIdentities;
    }
}

?>
