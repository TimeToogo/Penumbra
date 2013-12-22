<?php

namespace Storm\Drivers\Base\Relational\Relations;

use \Storm\Core\Containers\Map;
use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Traits\ForeignKey;

abstract class ToOneKeyedRelation extends KeyedRelation implements Relational\IToOneRelation {
        
    final protected function HashRowsByColumns(array $Rows, array $Columns) {
        $KeyedRows = array();
        foreach($Rows as $Row) {
            $Hash = $Row->GetDataFromColumns($Columns)->Hash();
            $KeyedRows[$Hash] = $Row;
        }
        
        return $KeyedRows;
    }
}

?>