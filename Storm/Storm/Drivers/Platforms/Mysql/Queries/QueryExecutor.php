<?php

namespace Storm\Drivers\Platforms\Mysql\Queries;

use \Storm\Core\Relational;
use \Storm\Drivers\Base\Relational\Table;
use \Storm\Drivers\Platforms\Base\Queries;
use \Storm\Drivers\Base\Relational\Queries\QueryBuilder;
use \Storm\Drivers\Base\Relational\Requests;
use \Storm\Drivers\Base\Relational\Expressions\Expression;
use \Storm\Drivers\Base\Relational\PrimaryKeys\ReturningDataKeyGenerator;

class QueryExecutor extends Queries\StandardQueryExecutor {
    public function __construct() {
        parent::__construct(new Persister());
    }
    
    protected function DeletePrimaryKeysQuery(QueryBuilder $QueryBuilder, Table $Table, array $PrimaryKeys) {
        $TableName = $Table->GetName();
        $QueryBuilder->AppendIdentifier('DELETE # FROM # WHERE ', [$TableName]);
        
        $PrimaryKeysColumns = $Table->GetPrimaryKeyColumns();
        $PrimaryKeyNames = array_keys($PrimaryKeysColumns);
        $QueryBuilder->Append('(');
        
        foreach($QueryBuilder->Delimit($PrimaryKeyNames, ', ') as $PrimaryKeyName) {
            $QueryBuilder->AppendIdentifier('#', [$TableName, $PrimaryKeyName]);
        }
        
        $QueryBuilder->Append(') IN (');
        
        $QueryBuilder->Append('(');
        foreach($QueryBuilder->Delimit($PrimaryKeys, '),(') as $PrimaryKey) {
            foreach($QueryBuilder->Delimit($PrimaryKeysColumns, ',') as $PrimaryKeysColumn) {
                $QueryBuilder->AppendColumnData($PrimaryKeysColumn, $PrimaryKey[$PrimaryKeysColumn]);
            }
        }
        $QueryBuilder->Append(')');
        
        $QueryBuilder->Append(')');
    }
}

?>