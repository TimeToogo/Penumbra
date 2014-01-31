<?php

namespace Storm\Drivers\Platforms\Mysql\Syncing;

use \Storm\Core\Containers\Map;
use \Storm\Drivers\Base\Relational\Syncing\IDatabaseBuilder;
use \Storm\Drivers\Base\Relational\Queries\IConnection;
use \Storm\Drivers\Base\Relational\Columns;
use \Storm\Drivers\Base\Relational\Traits;
use \Storm\Drivers\Dynamic\Relational;
use \Storm\Drivers\Platforms\Null;
use \Storm\Drivers\Platforms\Mysql;
use \Storm\Drivers\Platforms\Mysql\Tables;

class DatabaseBuilder implements IDatabaseBuilder {
    
    public function Build(IConnection $Connection) {
        $DatabaseName = $Connection->Prepare('SELECT DATABASE() AS `Name`')->Execute()->FetchRow()['Name'];
        
        $Tables = $this->BuildTables($Connection, $DatabaseName);
        
        return new Relational\Database(new Null\NullPlatform(), $Tables);
    }
    
    // <editor-fold defaultstate="collapsed" desc="Table Builders">
    private function BuildTables(IConnection $Connection, $DatabaseName, array &$Tables = array()) {
        $QueryBuilder = $Connection->QueryBuilder();
        $QueryBuilder->Append('SELECT `TABLE_NAME` FROM ');
        $QueryBuilder->Append('`INFORMATION_SCHEMA`.`TABLES` T ');
        $QueryBuilder->Append('WHERE TRUE ');
        $QueryBuilder->AppendValue('AND T.`TABLE_SCHEMA` = #', $DatabaseName);


        foreach ($QueryBuilder->Build()->Execute()->FetchAll() as $TableNameRow) {
            $this->BuildTable($Connection, $DatabaseName, $TableNameRow['TABLE_NAME'], $Tables);
        }


        return $Tables;
    }

    private function BuildTable(IConnection $Connection, $DatabaseName, $TableName, array &$LoadedTables) {
        if (isset($LoadedTables[$TableName]))
            return $LoadedTables[$TableName];

        $QueryBuilder = $Connection->QueryBuilder();
        $QueryBuilder->Append('SELECT T.*, C.CHARACTER_SET_NAME FROM ');
        $QueryBuilder->Append('`INFORMATION_SCHEMA`.`TABLES` T ');
        $QueryBuilder->Append('INNER JOIN ');
        $QueryBuilder->Append('`INFORMATION_SCHEMA`.`COLLATION_CHARACTER_SET_APPLICABILITY` C ');
        $QueryBuilder->Append('ON C.COLLATION_NAME = T.TABLE_COLLATION ');
        $QueryBuilder->Append('WHERE TRUE ');
        $QueryBuilder->AppendValue('AND T.`TABLE_SCHEMA` = # ', $DatabaseName);
        $QueryBuilder->AppendValue('AND T.`TABLE_NAME` = # ', $TableName);

        $TableInfoRow = $QueryBuilder->Build()->Execute()->FetchRow();
        
        $Columns = $this->BuildColumns($Connection, $DatabaseName, $TableName);
        
        $StructuralTraits = array();
        $RelationalTraits = array();
        
        $StructuralTraits[] = new Tables\Engine($TableInfoRow['ENGINE']);
        $StructuralTraits[] = new Tables\CharacterSet($TableInfoRow['CHARACTER_SET_NAME']);
        $StructuralTraits[] = new Tables\Collation($TableInfoRow['TABLE_COLLATION']);
        if (strlen($TableInfoRow['TABLE_COMMENT']) > 0) {
            $StructuralTraits[] = new Traits\Comment($TableInfoRow['TABLE_COMMENT']);
        }

        foreach ($this->BuildIndexes($Connection, $Columns, $DatabaseName, $TableName) as $Index) {
            $StructuralTraits[] = $Index;
        }
        
        $Table = new Relational\Table(
                $TableName, null, 
                $Columns, $StructuralTraits, array());
        $LoadedTables[$TableName] = $Table;
        
        $ForeignKeys = $this->BuildForeignKeys($Connection, $Columns, $DatabaseName, $TableName, $LoadedTables);
        
        foreach ($ForeignKeys as $ForeignKey) {
            $Table->AddTrait($ForeignKey);
        }

        return $Table;
    }
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Index Builders">
    private function BuildIndexes(IConnection $Connection, array $Columns, $DatabaseName, $TableName) {
        $QueryBuilder = $Connection->QueryBuilder();
        $QueryBuilder->Append('SELECT * FROM `INFORMATION_SCHEMA`.`STATISTICS` WHERE TRUE ');
        $QueryBuilder->AppendValue('AND `TABLE_SCHEMA` = # ', $DatabaseName);
        $QueryBuilder->AppendValue('AND `TABLE_NAME` = # ', $TableName);
        $QueryBuilder->AppendValue('AND `INDEX_NAME` != # ', 'PRIMARY');
        $QueryBuilder->Append('AND `INDEX_NAME` NOT IN (');
        $QueryBuilder->Append('SELECT `CONSTRAINT_NAME` FROM `INFORMATION_SCHEMA`.`REFERENTIAL_CONSTRAINTS` WHERE TRUE ');
        $QueryBuilder->AppendValue('AND `CONSTRAINT_SCHEMA` = # ', $DatabaseName);
        $QueryBuilder->AppendValue('AND `TABLE_NAME` = # ', $TableName);
        $QueryBuilder->Append(')');
        
        $IndexRowGroups = 
                $this->GroupColumnsByKey($QueryBuilder->Build()->Execute()->FetchAll(), 'INDEX_NAME');
        $Indexes = array();
        foreach($IndexRowGroups as $IndexRows) {
            $Indexes[] = $this->BuildIndex($Columns, $IndexRows);
        }
        
        return $Indexes;
    }
    
    private function BuildIndex(array $Columns, array $IndexKeyInfoRows) {
        $IndexType = null;
        $IndexInfoRow = $IndexKeyInfoRows[0];
        $IndexName = $IndexInfoRow['INDEX_NAME'];
        $StorageType = $IndexInfoRow['INDEX_TYPE'];
        if ($StorageType === 'FULLTEXT') {
            $IndexType = Traits\IndexType::FullText;
        }
        else if (!$IndexInfoRow['NON_UNIQUE']) {
            $IndexType = Traits\IndexType::Unique;
            $StorageType = $this->MapStorageType($StorageType);
        }
        else {
            $IndexType = Traits\IndexType::Plain;
            $StorageType = $this->MapStorageType($StorageType);
        }


        $IndexColumns = $this->GetColumnsFromRows($Columns, $IndexKeyInfoRows, 'SEQ_IN_INDEX', 'COLUMN_NAME');
        return new Tables\Index($IndexName, $IndexColumns, $IndexType, $StorageType);
    }

    private function MapStorageType($StorageType) {
        switch ($StorageType) {
            case 'BTREE':
                return Tables\IndexStorageType::BTree;
            case 'RTREE':
                return Tables\IndexStorageType::RTree;
            case 'HASH':
                return Tables\IndexStorageType::Hash;
            default:
                throw new \InvalidArgumentException('Unknown index storage type');
        }
    }
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Foreign Key Builders">
    private function BuildForeignKeys(IConnection $Connection, array $Columns, $DatabaseName, $TableName, array &$LoadedTables) {
        $ForeignKeys = array();
        
        $QueryBuilder = $Connection->QueryBuilder();
        $QueryBuilder->Append('SELECT C.*, R.`UPDATE_RULE`, R.`DELETE_RULE`  FROM ');
        $QueryBuilder->Append('`INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE` C ');
        $QueryBuilder->Append('INNER JOIN ');
        $QueryBuilder->Append('`INFORMATION_SCHEMA`.`REFERENTIAL_CONSTRAINTS` R ');
        $QueryBuilder->Append('ON C.`CONSTRAINT_NAME` = R.`CONSTRAINT_NAME` ');
        $QueryBuilder->Append('AND C.`TABLE_SCHEMA` = R.`CONSTRAINT_SCHEMA` ');
        $QueryBuilder->Append('AND C.`TABLE_NAME` = R.`TABLE_NAME` ');
        $QueryBuilder->Append('WHERE TRUE ');
        $QueryBuilder->AppendValue('AND C.`REFERENCED_TABLE_SCHEMA` = # ', $DatabaseName);
        $QueryBuilder->Append('AND C.`REFERENCED_TABLE_NAME` IS NOT NULL ');
        $QueryBuilder->Append('AND C.`REFERENCED_COLUMN_NAME` IS NOT NULL ');
        $QueryBuilder->AppendValue('AND C.`TABLE_SCHEMA` = # ', $DatabaseName);
        $QueryBuilder->AppendValue('AND C.`TABLE_NAME` = # ', $TableName);
        
        $ForeignKeyRowGroups = 
                $this->GroupColumnsByKey($QueryBuilder->Build()->Execute()->FetchAll(), 'CONSTRAINT_NAME');
        foreach($ForeignKeyRowGroups as $ForeignKeyRows) {
            $ForeignKeys[] = $this->BuildForeignKey($Connection, $DatabaseName, $Columns, $ForeignKeyRows, $LoadedTables);
        }
        
        return $ForeignKeys;
    }
    
    private function BuildForeignKey(IConnection $Connection, $DatabaseName, array $Columns, array $ForeignKeyRows, array &$LoadedTables) {
        $ForeignKeyRow = $ForeignKeyRows[0];
        $Name = $ForeignKeyRow['CONSTRAINT_NAME'];
        $ReferencedTableName = $ForeignKeyRow['REFERENCED_TABLE_NAME'];
        $ReferencedTable = 
                $this->BuildTable($Connection, $DatabaseName, $ReferencedTableName, $LoadedTables);

        $ReferencedColumnMap = new Map();
        foreach ($ForeignKeyRows as $ForeignKeyRow) {
            $ParentColumn = $Columns[$ForeignKeyRow['COLUMN_NAME']];
            $ReferencedColumn = $ReferencedTable->GetColumn($ForeignKeyRow['REFERENCED_COLUMN_NAME']);
            $ReferencedColumnMap[$ParentColumn] = $ReferencedColumn;
        }

        $UpdateMode = $this->MapForeignKeyMode($ForeignKeyRow['UPDATE_RULE']);
        $DeleteMode = $this->MapForeignKeyMode($ForeignKeyRow['DELETE_RULE']);

        return new Traits\ForeignKey($Name, $ReferencedColumnMap, $UpdateMode, $DeleteMode);
    }

    private function MapForeignKeyMode($Mode) {
        switch ($Mode) {
            case 'NO ACTION':
                return Traits\ForeignKeyMode::NoAction;
            case 'SET NULL':
                return Traits\ForeignKeyMode::SetNull;
            case 'CASCADE':
                return Traits\ForeignKeyMode::Cascade;
            default:
                throw new \InvalidArgumentException('Unknown ForeignKeyMode');
        }
    }
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Helpers">
    private function GroupColumnsByKey(array $Rows, $Key) {
        $GroupedRows = array();
        foreach ($Rows as $Row) {
            $GroupValue = $Row[$Key];
            if (!isset($GroupedRows[$GroupValue]))
                $GroupedRows[$GroupValue] = array();


            $GroupedRows[$GroupValue][] = $Row;
        }


        return $GroupedRows;
    }


    private function GetColumnsFromRows(array $Columns, array $InfoRows, $PositionKey, $ColumnNameKey) {
        $RetrievedColumns = array();
        foreach ($InfoRows as $Row) {
            $RetrievedColumns[$Row[$PositionKey]] = $Columns[$Row[$ColumnNameKey]];
        }
        ksort($RetrievedColumns);


        return $RetrievedColumns;
    }
   // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Column Builders">
    private function BuildColumns(IConnection $Connection, $DatabaseName, $TableName) {
        $QueryBuilder = $Connection->QueryBuilder();
        $QueryBuilder->Append('SELECT * FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE TRUE ');
        $QueryBuilder->AppendValue('AND `TABLE_SCHEMA` = # ', $DatabaseName);
        $QueryBuilder->AppendValue('AND `TABLE_NAME` = # ', $TableName);
        $QueryBuilder->AppendValue('ORDER BY `ORDINAL_POSITION`', $TableName);
        
        $Columns = array();
        foreach($QueryBuilder->Build()->Execute()->FetchAll() as $ColumnRow) {
            $Column = $this->BuildColumn($Connection, $ColumnRow);
            $Columns[$Column->GetName()] = $Column;
        }
        
        return $Columns;
    }
    
    private function BuildColumn(IConnection $Connection, array $ColumnInfoRow) {
        $Name = $ColumnInfoRow['COLUMN_NAME'];
        $DataType = $this->BuildDataType($ColumnInfoRow);
        $Traits = array();
        if ($ColumnInfoRow['IS_NULLABLE'] === 'YES') {
            $Traits[] = new Columns\Traits\DefaultValue($ColumnInfoRow['COLUMN_DEFAULT']);
        }         
        else {
            $Traits[] = new Columns\Traits\NotNullable();
        }
        if($ColumnInfoRow['CHARACTER_SET_NAME'] !== null) {
            $Traits[] = new Mysql\Columns\CharacterSet($ColumnInfoRow['CHARACTER_SET_NAME']);
        }
        if($ColumnInfoRow['COLLATION_NAME'] !== null) {
            $Traits[] = new Mysql\Columns\Collation($ColumnInfoRow['COLLATION_NAME']);
        }
        if (strpos($ColumnInfoRow['EXTRA'], 'auto_increment') !== false) {
            $Traits[] = new Columns\Traits\Increment();
        }
        if (strlen($ColumnInfoRow['COLUMN_COMMENT']) > 0) {
            $Traits[] = new Columns\Traits\Comment($ColumnInfoRow['COLUMN_COMMENT']);
        }
        $IsPrimaryKey = $ColumnInfoRow['COLUMN_KEY'] === 'PRI';
        
        return new Columns\Column($Name, $DataType, $IsPrimaryKey, $Traits);
    }


    private function BuildDataType(array $ColumnInfoRow) {
        $DataTypeName = $ColumnInfoRow['DATA_TYPE'];

        $DataTypeDefintion = $ColumnInfoRow['COLUMN_TYPE'];
        $DataTypeDefintion = trim(str_replace($DataTypeName, '', $DataTypeDefintion));
        $DataTypeExtra = null;
        $DataTypeParameters = array();

        if (strlen($DataTypeDefintion) > 0) {
            if (strpos($DataTypeDefintion, '(') !== false && strpos($DataTypeDefintion, ')') !== false) {
                $DataTypeExtra = trim(substr($DataTypeDefintion, strrpos($DataTypeDefintion, ')') + 1));
                if(strlen($DataTypeExtra) > 0) {
                    $DataTypeDefintion = substr($DataTypeDefintion, 0, -strlen($DataTypeExtra));
                }
                else
                    $DataTypeExtra = null;

                $DataTypeDefintion = trim($DataTypeDefintion, '()');
                $DataTypeParameters = explode(',', $DataTypeDefintion);
                array_walk($DataTypeParameters, function (&$Parameter, $Key) {
                    $UnquotedParameter = trim($Parameter, '\'"');
                    if ($UnquotedParameter !== $Parameter)
                        $Parameter = $UnquotedParameter;
                    else if (is_numeric($Parameter))
                        $Parameter = $Parameter + 0;
                });
            }
        }

        return new \Storm\Drivers\Base\Relational\Columns\DataType($DataTypeName, $DataTypeParameters, $DataTypeExtra);
    }
    // </editor-fold>
}

?>