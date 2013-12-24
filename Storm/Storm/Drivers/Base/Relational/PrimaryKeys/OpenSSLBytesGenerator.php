<?php

namespace Storm\Drivers\Base\Relational\PrimaryKeys;

use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\Queries\IConnection;

class OpenSSLBytesGenerator extends SingleKeyGenerator {
    private $Length;
    private $Hexadecimal;
    public function __construct($Length, $Hexadecimal = true) {
        $this->Length = $Length;
        $this->Hexadecimal = $Hexadecimal;
    }

    
    protected function FillSinglePrimaryKeys(IConnection $Connection, Relational\Table $Table, 
            array $PrimaryKeys, Relational\Columns\Column $Column) {
        $BytesToGenerate = $this->Length * count($PrimaryKeys);
        $Bytes = openssl_random_pseudo_bytes($BytesToGenerate);
        foreach($PrimaryKeys as $PrimaryKey) {
            $CurrentBytes = substr($Bytes, 0, $this->Length);
            $PrimaryKey[$Column] = $this->Hexadecimal ? bin2hex($CurrentBytes) : $CurrentBytes;
            
            $Bytes = substr($Bytes, $this->Length);
        }
    }
}

?>
