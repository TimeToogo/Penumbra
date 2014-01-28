<?php

namespace Storm\Drivers\Base\Relational\PrimaryKeys;

use \Storm\Core\Containers\Map;
use \Storm\Drivers\Base\Relational;
use \Storm\Drivers\Base\Relational\Queries\IConnection;

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
                throw new \Exception('Unmapped primary key: ' . $PrimaryKeyColumn->GetName());
            }
        }
    }
    
    public function FillPrimaryKeys(IConnection $Connection, array &$UnkeyedRows) {
        $PrimaryKeyColumns = $this->GetPrimaryKeyColumns();
        foreach($PrimaryKeyColumns as $PrimaryKeyColumn) {
            $KeyGenerator = $this->ColumnGeneratorMap[$PrimaryKeyColumn];
            $KeyGenerator->FillPrimaryKeys($Connection, $UnkeyedRows);
        }
    }
}

?>
