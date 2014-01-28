<?php

namespace Storm\Drivers\Base\Relational\PrimaryKeys;

use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\Queries\IConnection;

class PHPUniqueIdGenerator extends PreInsertKeyGenerator {
    private $Prefix;
    private $MoreEntropy;
    public function __construct($Prefix = '', $MoreEntropy = true) {
        $this->Prefix = $Prefix;
        $this->MoreEntropy = $MoreEntropy;
    }
    
    public function FillPrimaryKeys(IConnection $Connection, array &$UnkeyedRows) {
        $Columns = $this->GetPrimaryKeyColumns();
        
        foreach($UnkeyedRows as &$UnkeyedRow) {
            foreach($Columns as $Column) {
                $UnkeyedRow[$Column->GetIdentifier()] = uniqid($this->Prefix, $this->MoreEntropy);
            }
        }
    }
}

?>
