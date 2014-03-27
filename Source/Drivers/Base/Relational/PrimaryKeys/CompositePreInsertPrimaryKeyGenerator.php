<?php

namespace Penumbra\Drivers\Base\Relational\PrimaryKeys;

use \Penumbra\Core\Containers\Map;
use \Penumbra\Drivers\Base\Relational;
use \Penumbra\Drivers\Base\Relational\Queries\IConnection;

class CompositePreInsertPrimaryKeyGenerator extends PreInsertKeyGenerator {
    private $ColumnGeneratorMap;
    public function __construct() {
        $this->ColumnGeneratorMap = new Map();
    }
        
    final public function Map(Relational\Columns\Column $Column, PreInsertKeyGenerator $Generator) {
        $this->ColumnGeneratorMap[$Column] = $Generator;
        
        return $this;
    }
    
    protected function OnSetPrimaryKeyColumns(array $PrimaryKeyColumns) {
        foreach($PrimaryKeyColumns as $PrimaryKeyColumn) {
            if(!isset($this->ColumnGeneratorMap[$PrimaryKeyColumn])) {
                throw new \Penumbra\Core\Relational\InvalidColumnException(
                        'The supplied primary key column %s is not mapped to a key generator',
                        $PrimaryKeyColumn->GetName());
            }
        }
    }
    
    public function FillPrimaryKeys(IConnection $Connection, array $UnkeyedRows) {
        $PrimaryKeyColumns = $this->GetPrimaryKeyColumns();
        foreach($PrimaryKeyColumns as $PrimaryKeyColumn) {
            $KeyGenerator = $this->ColumnGeneratorMap[$PrimaryKeyColumn];
            $KeyGenerator->FillPrimaryKeys($Connection, $UnkeyedRows);
        }
    }
}

?>
