<?php

namespace Storm\Drivers\Base\Relational\PrimaryKeys;

use \Storm\Core\Containers\Map;
use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\Queries\IConnection;

class CompositePrimaryKeyGenerator extends KeyGenerator {
    private $ColumnGeneratorMap;
    public function __construct() {
        $this->ColumnGeneratorMap = new Map();
    }
    
    final public function Map(Relational\Columns\Column $Column, IKeyGenerator $Generator) {
        $this->ColumnGeneratorMap[$Column] = $Generator;
        
        return $this;
    }
    
    public function FillAllPrimaryKeys(IConnection $Connection, Relational\Table $Table, 
            array $PrimaryKeys, array $PrimaryKeyColumns) {
        foreach($PrimaryKeyColumns as $PrimaryKeyColumn) {
            if(!isset($this->ColumnGeneratorMap[$PrimaryKeyColumn]))
                throw new \InvalidArgumentException('Supplied primary key column is not mapped to a key generator');
            
            $KeyGenerator = $this->ColumnGeneratorMap[$PrimaryKeyColumn];
            $KeyGenerator->FillPrimaryKeys($Connection, $Table, $PrimaryKeys, [$PrimaryKeyColumn]);
        }
    }
}

?>
