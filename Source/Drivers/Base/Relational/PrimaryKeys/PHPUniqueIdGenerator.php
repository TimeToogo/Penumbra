<?php

namespace Penumbra\Drivers\Base\Relational\PrimaryKeys;

use \Penumbra\Drivers\Base\Relational;
use \Penumbra\Drivers\Base\Relational\Queries\IConnection;

class PHPUniqueIdGenerator extends PreInsertKeyGenerator {
    private $Prefix;
    private $MoreEntropy;
    public function __construct($Prefix = '', $MoreEntropy = true) {
        $this->Prefix = $Prefix;
        $this->MoreEntropy = $MoreEntropy;
    }
    
    public function FillPrimaryKeys(IConnection $Connection, array $UnkeyedRows) {
        $Columns = $this->GetPrimaryKeyColumns();
        
        foreach($UnkeyedRows as $UnkeyedRow) {
            foreach($Columns as $Column) {
                $UnkeyedRow[$Column] = uniqid($this->Prefix, $this->MoreEntropy);
            }
        }
    }
}

?>
