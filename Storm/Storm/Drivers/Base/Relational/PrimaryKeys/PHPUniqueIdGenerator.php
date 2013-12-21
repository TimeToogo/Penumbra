<?php

namespace Storm\Drivers\Base\Relational\PrimaryKeys;

use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\Queries\IConnection;

class PHPUniqueIdGenerator extends SingleKeyGenerator {
    private $Prefix;
    public function __construct($Prefix = '') {
        $this->Prefix = $Prefix;
    }
    
    protected function FillSinglePrimaryKeys(IConnection $Connection, Relational\Table $Table, 
            array $PrimaryKeys, Relational\Columns\Column $Column) {
        foreach($PrimaryKeys as $PrimaryKey) {
            $PrimaryKey[$Column] = uniqid($this->Prefix, true);
        }
    }
}

?>
