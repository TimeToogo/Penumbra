<?php

namespace Storm\Drivers\Base\Relational\PrimaryKeys;

use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\Queries\IConnection;

class OpenSSLBytesGenerator extends PreInsertKeyGenerator {
    private $Length;
    private $Hexadecimal;
    public function __construct($Length, $Hexadecimal = true) {
        $this->Length = $Length;
        $this->Hexadecimal = $Hexadecimal;
    }

    public function FillPrimaryKeys(IConnection $Connection, array &$UnkeyedRows) {
        $Columns = $this->GetPrimaryKeyColumns();
        $BytesToGenerate = $this->Length * count($UnkeyedRows) * count($Columns);
        $Bytes = openssl_random_pseudo_bytes($BytesToGenerate);
        
        foreach($UnkeyedRows as &$UnkeyedRow) {
            foreach($Columns as $Column) {
                $CurrentBytes = substr($Bytes, 0, $this->Length);
                $UnkeyedRow[$Column->GetIdentifier()] = $this->Hexadecimal ? bin2hex($CurrentBytes) : $CurrentBytes;
                $Bytes = substr($Bytes, $this->Length);
            }
        }
    }
}

?>
